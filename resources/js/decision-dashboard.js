import Chart from 'chart.js/auto';

function createDecisionCharts() {
    const data = window.PNAEDecisionDashboard;

    if (!data) {
        return;
    }

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
        },
    };

    const applicationsCanvas = document.getElementById(
        'decisionApplicationsChart'
    );

    if (applicationsCanvas) {
        new Chart(applicationsCanvas, {
            type: 'line',
            data: {
                labels: data.monthly.labels,
                datasets: [
                    {
                        label: 'Demandes',
                        data: data.monthly.applications,
                        borderColor: '#0b6edc',
                        backgroundColor: 'rgba(11, 110, 220, 0.16)',
                        fill: true,
                        tension: 0.3,
                    },
                ],
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                        },
                    },
                },
            },
        });
    }

    const revenueCanvas = document.getElementById(
        'decisionRevenueChart'
    );

    if (revenueCanvas) {
        new Chart(revenueCanvas, {
            type: 'bar',
            data: {
                labels: data.monthly.labels,
                datasets: [
                    {
                        label: 'Recettes FCFA',
                        data: data.monthly.revenues,
                        backgroundColor: 'rgba(13, 148, 136, 0.65)',
                        borderColor: '#0d9488',
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    }

    const statusCanvas = document.getElementById(
        'decisionStatusChart'
    );

    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: data.applicationStatuses.labels,
                datasets: [
                    {
                        data: data.applicationStatuses.values,
                        backgroundColor: [
                            '#3b82f6',
                            '#f59e0b',
                            '#16a34a',
                            '#dc2626',
                            '#0d9488',
                        ],
                        borderWidth: 0,
                    },
                ],
            },
            options: commonOptions,
        });
    }

    const paymentCanvas = document.getElementById(
        'decisionPaymentChart'
    );

    if (paymentCanvas) {
        new Chart(paymentCanvas, {
            type: 'doughnut',
            data: {
                labels: data.paymentStatuses.labels,
                datasets: [
                    {
                        data: data.paymentStatuses.values,
                        backgroundColor: [
                            '#f59e0b',
                            '#16a34a',
                            '#dc2626',
                        ],
                        borderWidth: 0,
                    },
                ],
            },
            options: commonOptions,
        });
    }

    const dailyCanvas = document.getElementById(
        'decisionDailyChart'
    );

    if (dailyCanvas) {
        new Chart(dailyCanvas, {
            type: 'bar',
            data: {
                labels: data.dailyActivity.labels,
                datasets: [
                    {
                        label: 'Demandes',
                        data: data.dailyActivity.values,
                        backgroundColor: '#0b6edc',
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                        },
                    },
                },
            },
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener(
        'DOMContentLoaded',
        createDecisionCharts
    );
} else {
    createDecisionCharts();
}