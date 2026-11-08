import { screen, within } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import LiveMessages from '@/Components/Live/LiveMessages.vue';
import { livePlayer } from '@/testing/live';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveMessage } from '@/types/live';

const players = [
  livePlayer(11, 'Anna'),
  livePlayer(12, 'Bernd'),
  livePlayer(13, '<img src=x onerror=alert(1)>'),
];

function renderMessages(props: {
  messages: LiveMessage[];
  open?: boolean;
  onOpen?: (open: boolean) => void;
}) {
  return renderWithUi(LiveMessages, {
    players,
    open: true,
    ...props,
    'onUpdate:open': props.onOpen,
  });
}

describe('LiveMessages', () => {
  it('setzt den Namen fett aus der Spielerliste vor den Text', () => {
    renderMessages({ messages: [{ spielerId: 11, text: 'spielt eine Hochzeit!' }] });

    const item = screen.getByText('spielt eine Hochzeit!', { exact: false }).closest('li')!;
    expect(within(item).getByText('Anna').tagName).toBe('B');
    expect(item.textContent).toBe('Anna spielt eine Hochzeit!');
  });

  it('zeigt Meldungen ohne Spieler nur als Text', () => {
    renderMessages({ messages: [{ spielerId: null, text: 'Es wird ein Normalspiel gespielt!' }] });

    const item = screen.getByText('Es wird ein Normalspiel gespielt!').closest('li')!;
    expect(item.querySelector('b')).toBeNull();
  });

  it('lässt den Namen weg, wenn die Spieler-ID nicht in der Liste steht', () => {
    renderMessages({ messages: [{ spielerId: 99, text: 'Schweinchen!' }] });

    expect(screen.getByText('Schweinchen!').closest('li')!.textContent).toBe('Schweinchen!');
  });

  it('gibt HTML in Namen und Texten als Text aus', () => {
    renderMessages({
      messages: [
        { spielerId: 13, text: 'Re!' },
        { spielerId: null, text: '<b>fett</b> <script>alert(1)</script>' },
      ],
    });

    const list = screen.getByRole('list');
    expect(list.querySelector('img')).toBeNull();
    expect(list.querySelector('script')).toBeNull();
    expect(list.querySelectorAll('b')).toHaveLength(1);
    expect(screen.getByText('<img src=x onerror=alert(1)>')).toBeTruthy();
    expect(list.textContent).toContain('<b>fett</b> <script>alert(1)</script>');
  });

  it('zeigt die neueste Meldung zuerst, so wie die Engine sie liefert', () => {
    renderMessages({
      messages: [
        { spielerId: 12, text: 'Re!' },
        { spielerId: null, text: 'Es wird ein Normalspiel gespielt!' },
      ],
    });

    const items = within(screen.getByRole('list')).getAllByRole('listitem');
    expect(items.map((item) => item.textContent)).toEqual([
      'Bernd Re!',
      'Es wird ein Normalspiel gespielt!',
    ]);
  });

  it('bleibt eingeklappt unsichtbar', () => {
    renderMessages({ open: false, messages: [{ spielerId: 11, text: 'Re!' }] });

    expect(screen.queryByRole('region', { name: 'Meldungen' })).toBeNull();
  });

  it('lässt sich über den Schließen-Knopf einklappen', async () => {
    const user = userEvent.setup();
    const onOpen = vi.fn();
    renderMessages({ messages: [{ spielerId: 11, text: 'Re!' }], onOpen });

    await user.click(screen.getByRole('button', { name: 'Meldungen ausblenden' }));

    expect(onOpen).toHaveBeenCalledWith(false);
  });

  it('klappt bei einer neuen Meldung auf', async () => {
    const onOpen = vi.fn();
    const first: LiveMessage = { spielerId: null, text: 'Es wird ein Normalspiel gespielt!' };
    const { rerender } = renderMessages({ open: false, messages: [first], onOpen });
    expect(onOpen).not.toHaveBeenCalled();

    await rerender({ messages: [{ spielerId: 11, text: 'Re!' }, first] });

    expect(onOpen).toHaveBeenCalledWith(true);
  });

  it('klappt nicht auf, wenn ein Neuladen dieselben Meldungen liefert', async () => {
    const onOpen = vi.fn();
    const { rerender } = renderMessages({
      open: false,
      messages: [{ spielerId: 11, text: 'Re!' }],
      onOpen,
    });

    await rerender({ messages: [{ spielerId: 11, text: 'Re!' }] });

    expect(onOpen).not.toHaveBeenCalled();
  });

  it('klappt nicht auf, wenn ein neues Spiel mit leerem Protokoll beginnt', async () => {
    const onOpen = vi.fn();
    const { rerender } = renderMessages({
      open: false,
      messages: [{ spielerId: 11, text: 'Re!' }],
      onOpen,
    });

    await rerender({ messages: [] });

    expect(onOpen).not.toHaveBeenCalled();
  });
});
