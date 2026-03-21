import { onBeforeUnmount, ref, watch, type Ref } from 'vue';
import Chart from 'chart.js/auto';
import { useAppRoute } from '@/composables/useAppRoute';

type HomeChartResponse = {
  gameDates: string[];
  gameCounter: number[];
};

export function useHomeChart(canvasRef: Ref<HTMLCanvasElement | null>, groupId: number): {
  isLoading: Ref<boolean>;
  error: Ref<string | null>;
} {
  const { route } = useAppRoute();
  const isLoading = ref(false);
  const error = ref<string | null>(null);
  let chart: Chart | null = null;

  const destroyChart = (): void => {
    if (chart) {
      chart.destroy();
      chart = null;
    }
  };

  const loadChart = async (): Promise<void> => {
    if (!canvasRef.value) {
      return;
    }

    isLoading.value = true;
    error.value = null;
    destroyChart();

    try {
      const response = await fetch(route('charts.home', { group: groupId }), {
        headers: {
          Accept: 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error('Chart-Daten konnten nicht geladen werden.');
      }

      const data = (await response.json()) as HomeChartResponse;

      Chart.defaults.font.family = '"Open Sans"';

      chart = new Chart(canvasRef.value, {
        type: 'line',
        data: {
          labels: data.gameDates,
          datasets: [
            {
              borderColor: '#E37222',
              backgroundColor: 'rgba(244,158,98,0.3)',
              data: data.gameCounter,
            },
          ],
        },
        options: {
          animation: false,
          maintainAspectRatio: false,
          scales: {
            x: {
              ticks: {
                maxTicksLimit: 10,
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
    } catch {
      error.value = 'Das Chart konnte nicht geladen werden.';
    } finally {
      isLoading.value = false;
    }
  };

  watch(
    () => canvasRef.value,
    (canvas) => {
      if (canvas) {
        void loadChart();
      }
    },
    { immediate: true },
  );

  onBeforeUnmount(() => {
    destroyChart();
  });

  return {
    isLoading,
    error,
  };
}
