'use strict';

(function () {
  const data = window.projectAnalyticsData || {};
  const cardColor = config.colors.white;
  const axisColor = config.colors.axisColor;
  const borderColor = config.colors.borderColor;

  function renderDonut(elId, labels, series, colors) {
    const el = document.querySelector(elId);
    if (!el) {
      return;
    }

    new ApexCharts(el, {
      chart: { type: 'donut', height: 280 },
      labels: labels,
      series: series,
      colors: colors || [config.colors.secondary, config.colors.info, config.colors.success, config.colors.warning],
      legend: { position: 'bottom', labels: { colors: axisColor } },
      dataLabels: { enabled: true },
      plotOptions: {
        pie: {
          donut: { size: '65%' },
        },
      },
    }).render();
  }

  function renderBar(elId, labels, series, horizontal) {
    const el = document.querySelector(elId);
    if (!el) {
      return;
    }

    new ApexCharts(el, {
      chart: { type: 'bar', height: 300, toolbar: { show: false } },
      plotOptions: {
        bar: {
          horizontal: !!horizontal,
          columnWidth: horizontal ? undefined : '45%',
          borderRadius: 6,
        },
      },
      series: series,
      colors: [config.colors.primary, config.colors.info, config.colors.success],
      dataLabels: { enabled: false },
      xaxis: {
        categories: labels,
        labels: { style: { colors: axisColor } },
      },
      yaxis: { labels: { style: { colors: axisColor } } },
      grid: { borderColor: borderColor },
      legend: { labels: { colors: axisColor } },
    }).render();
  }

  function renderLine(elId, labels, series) {
    const el = document.querySelector(elId);
    if (!el) {
      return;
    }

    new ApexCharts(el, {
      chart: { type: 'line', height: 280, toolbar: { show: false } },
      series: [{ name: 'Completed Tasks', data: series }],
      colors: [config.colors.primary],
      stroke: { curve: 'smooth', width: 3 },
      xaxis: {
        categories: labels,
        labels: { style: { colors: axisColor } },
      },
      yaxis: { labels: { style: { colors: axisColor } } },
      grid: { borderColor: borderColor },
      markers: { size: 4 },
    }).render();
  }

  if (data.aggregateTaskStatus) {
    const agg = data.aggregateTaskStatus;
    renderDonut(
      '#aggregateTaskStatusChart',
      ['Pending', 'In Progress', 'Completed', 'Blocked'],
      [agg.todo || 0, agg.in_progress || 0, agg.done || 0, agg.blocked || 0]
    );
  }

  if (data.taskStatus) {
    renderDonut('#taskStatusChart', data.taskStatus.labels, data.taskStatus.series);
  }

  if (data.hoursComparison) {
    renderBar('#hoursComparisonChart', data.hoursComparison.labels, [
      { name: 'Estimated', data: data.hoursComparison.estimated },
      { name: 'Actual', data: data.hoursComparison.actual },
    ]);
  }

  if (data.workload) {
    renderBar(
      '#workloadChart',
      data.workload.labels,
      [{ name: 'Hours Logged', data: data.workload.series }],
      true
    );
  }

  if (data.efficiency) {
    renderBar(
      '#efficiencyChart',
      data.efficiency.labels,
      [{ name: 'Efficiency Score', data: data.efficiency.series }],
      true
    );
  }

  if (data.completionTrend) {
    renderLine('#completionTrendChart', data.completionTrend.labels, data.completionTrend.series);
  }
})();
