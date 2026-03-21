import { computed, inject, onBeforeUnmount, ref, watch, type Ref } from 'vue';
import Chart from 'chart.js/auto';
import { inertiaColorModeKey } from '@/composables/inertiaColorMode';
import type { HomeGamesChartPayload } from '@/types/home';

function chartPalette(isDark: boolean): {
  borderColor: string;
  fillColor: string;
  tickColor: string;
  gridColor: string;
} {
  if (isDark) {
    return {
      borderColor: '#f4a574',
      fillColor: 'rgba(227, 114, 34, 0.22)',
      tickColor: '#94a3b8',
      gridColor: 'rgba(148, 163, 184, 0.18)',
    };
  }

  return {
    borderColor: '#E37222',
    fillColor: 'rgba(244, 158, 98, 0.35)',
    tickColor: '#475569',
    gridColor: 'rgba(100, 116, 139, 0.2)',
  };
}

/**
 * Rendert das Spiele-Zeitreihen-Chart aus servergelieferten Daten (Inertia-Prop).
 */
export function useHomeChart(
  canvasRef: Ref<HTMLCanvasElement | null>,
  chartData: Ref<HomeGamesChartPayload | null | undefined>,
): {
  error: Ref<string | null>;
} {
  const colorMode = inject(inertiaColorModeKey);
  if (colorMode === undefined) {
    throw new Error('useHomeChart: Farbmodus fehlt — Seite muss in AppLayout liegen.');
  }
  const isDark = computed(() => colorMode.value === 'dark');
  const error = ref<string | null>(null);
  let chart: Chart | null = null;
  let lastPayload: HomeGamesChartPayload | null = null;

  const destroyChart = (): void => {
    if (chart) {
      chart.destroy();
      chart = null;
    }
  };

  const renderFromPayload = (data: HomeGamesChartPayload, animate: boolean): void => {
    error.value = null;

    if (!canvasRef.value) {
      return;
    }

    try {
      const palette = chartPalette(isDark.value);

      destroyChart();

      Chart.defaults.font.family = '"Open Sans"';

      chart = new Chart(canvasRef.value, {
        type: 'line',
        data: {
          labels: data.gameDates,
          datasets: [
            {
              borderColor: palette.borderColor,
              backgroundColor: palette.fillColor,
              data: data.gameCounter,
            },
          ],
        },
        options: {
          animation: animate
            ? {
                duration: 900,
                easing: 'easeOutQuart',
              }
            : false,
          maintainAspectRatio: false,
          scales: {
            x: {
              ticks: {
                maxTicksLimit: 10,
                color: palette.tickColor,
              },
              grid: {
                color: palette.gridColor,
              },
              border: {
                color: palette.gridColor,
              },
            },
            y: {
              ticks: {
                color: palette.tickColor,
              },
              grid: {
                color: palette.gridColor,
              },
              border: {
                color: palette.gridColor,
              },
            },
          },
          plugins: {
            tooltip: {
              mode: 'index',
              intersect: false,
            },
            legend: {
              display: false,
            },
          },
          elements: {
            point: {
              radius: 0,
              hitRadius: 10,
            },
            line: {
              fill: true,
              tension: 0.2,
              borderWidth: 1.1,
            },
          },
        },
      });

      lastPayload = data;
    } catch {
      error.value = 'Das Chart konnte nicht dargestellt werden.';
      lastPayload = null;
      destroyChart();
    }
  };

  watch(
    [canvasRef, chartData],
    ([canvas, data]) => {
      if (!canvas || data == null) {
        return;
      }

      renderFromPayload(data, true);
    },
    { immediate: true, deep: true },
  );

  watch(isDark, () => {
    if (lastPayload) {
      renderFromPayload(lastPayload, false);
    }
  });

  onBeforeUnmount(() => {
    destroyChart();
  });

  return {
    error,
  };
}
