document.addEventListener('DOMContentLoaded', function() {
    const chartButtons = document.querySelectorAll('.chart-btn');
    chartButtons.forEach(button => {
        button.addEventListener('click', function() {
            chartButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });

    initVisitorChart();
    initSourceChart();
    initDeviceChart();
});

function initVisitorChart() {
    const ctx = document.getElementById('visitorChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Visitors',
                data: [3450, 4200, 3800, 5100, 4800, 6200, 5900],
                borderColor: '#007aff',
                backgroundColor: 'rgba(0, 122, 255, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#007aff',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#007aff',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    padding: 12,
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toLocaleString() + ' visitors';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.06)',
                        drawBorder: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
}

function initSourceChart() {
    const ctx = document.getElementById('sourceChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Organic', 'Direct', 'Social', 'Referral', 'Email'],
            datasets: [{
                label: 'Traffic',
                data: [35, 28, 20, 12, 5],
                backgroundColor: '#5856d6',
                borderColor: '#5856d6',
                borderWidth: 0,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    padding: 12,
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.06)',
                        drawBorder: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        },
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    max: 40
                }
            }
        }
    });
}

function initDeviceChart() {
    const ctx = document.getElementById('deviceChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Desktop', 'Mobile', 'Tablet'],
            datasets: [{
                data: [55, 35, 10],
                backgroundColor: [
                    '#007aff',
                    '#5856d6',
                    '#af52de'
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        padding: 15,
                        font: {
                            size: 12
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    padding: 12,
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            return label + ': ' + value + '%';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
}

