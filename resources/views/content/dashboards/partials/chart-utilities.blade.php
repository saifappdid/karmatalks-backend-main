<script>
    // ========================================
    // Modern Chart Utilities with Dark Mode
    // ========================================

    // Detect dark mode for charts
    const isDarkMode = document.documentElement.classList.contains('dark-layout');

    const chartTheme = {
        mode: isDarkMode ? 'dark' : 'light',
        background: 'transparent',
        foreColor: isDarkMode ? '#b4b7bd' : '#6c757d'
    };

    const gridColor = isDarkMode ? '#3b4253' : '#e9ecef';
    const strokeColors = isDarkMode ? '#283046' : '#ffffff';

    // Modern color palette
    const modernColors = {
        primary: '#667eea',
        purple: '#764ba2',
        success: '#11998e',
        green: '#38ef7d',
        warning: '#f5576c',
        pink: '#f093fb',
        info: '#4facfe',
        cyan: '#00f2fe',
        orange: '#ff6b6b',
        yellow: '#feca57'
    };

    const colorPalette = [
        modernColors.primary,
        modernColors.success,
        modernColors.info,
        modernColors.warning,
        modernColors.purple,
        modernColors.orange
    ];

    // Enhanced Area Chart
    function initModernAreaChart({
        selector,
        categories,
        data,
        label = 'Value',
        color = modernColors.primary
    }) {
        const chartEl = document.querySelector(selector);
        if (!chartEl || !categories.length) return;

        const options = {
            series: [{
                name: label,
                data: data
            }],
            chart: {
                type: 'area',
                height: 280,
                background: 'transparent',
                fontFamily: 'Inter, sans-serif',
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            theme: chartTheme,
            colors: [color],
            fill: {
                type: 'gradient',
                gradient: {
                    shade: isDarkMode ? 'dark' : 'light',
                    shadeIntensity: 0.5,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: categories,
                labels: {
                    rotate: -45,
                    style: {
                        colors: chartTheme.foreColor,
                        fontSize: '11px'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: chartTheme.foreColor
                    }
                }
            },
            grid: {
                borderColor: gridColor,
                strokeDashArray: 4
            },
            tooltip: {
                theme: isDarkMode ? 'dark' : 'light'
            }
        };

        new ApexCharts(chartEl, options).render();
    }

    // Enhanced Bar Chart
    function initModernBarChart({
        selector,
        categories,
        data,
        label = 'Value',
        colors = [modernColors.primary, modernColors.purple],
        horizontal = false
    }) {
        const chartEl = document.querySelector(selector);
        if (!chartEl || !categories.length) return;

        const options = {
            series: [{
                name: label,
                data: data
            }],
            chart: {
                type: 'bar',
                height: 280,
                background: 'transparent',
                fontFamily: 'Inter, sans-serif',
                toolbar: {
                    show: true
                }
            },
            theme: chartTheme,
            colors: colors,
            fill: {
                type: 'gradient',
                gradient: {
                    shade: isDarkMode ? 'dark' : 'light',
                    type: horizontal ? 'horizontal' : 'vertical',
                    shadeIntensity: 0.3,
                    gradientToColors: [colors[1] || colors[0]],
                    opacityFrom: 1,
                    opacityTo: 0.8
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: horizontal ? 6 : 8,
                    columnWidth: '60%',
                    horizontal: horizontal
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: categories,
                labels: {
                    rotate: horizontal ? 0 : -45,
                    style: {
                        colors: chartTheme.foreColor,
                        fontSize: '11px'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: chartTheme.foreColor
                    }
                }
            },
            grid: {
                borderColor: gridColor,
                strokeDashArray: 4
            },
            tooltip: {
                theme: isDarkMode ? 'dark' : 'light'
            }
        };

        new ApexCharts(chartEl, options).render();
    }

    // Enhanced Donut Chart
    function initModernDonutChart({
        selector,
        labels,
        data,
        colors = colorPalette,
        centerLabel = 'Total'
    }) {
        const chartEl = document.querySelector(selector);
        if (!chartEl || !labels.length) return;

        const total = data.reduce((a, b) => a + b, 0);

        const options = {
            series: data,
            chart: {
                type: 'donut',
                height: 280,
                background: 'transparent',
                fontFamily: 'Inter, sans-serif'
            },
            theme: chartTheme,
            labels: labels,
            colors: colors,
            stroke: {
                width: 3,
                colors: [strokeColors]
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => Math.round(val) + '%',
                style: {
                    fontSize: '12px',
                    fontWeight: 600
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: {
                                fontSize: '16px',
                                fontWeight: 600,
                                color: chartTheme.foreColor
                            },
                            value: {
                                fontSize: '24px',
                                fontWeight: 700,
                                color: chartTheme.foreColor,
                                formatter: (val) => parseInt(val).toLocaleString()
                            },
                            total: {
                                show: true,
                                label: centerLabel,
                                fontSize: '14px',
                                fontWeight: 500,
                                color: chartTheme.foreColor,
                                formatter: () => total.toLocaleString()
                            }
                        }
                    }
                }
            },
            legend: {
                position: 'bottom',
                fontSize: '13px',
                fontWeight: 500,
                labels: {
                    colors: chartTheme.foreColor
                },
                itemMargin: {
                    horizontal: 12,
                    vertical: 8
                }
            },
            tooltip: {
                theme: isDarkMode ? 'dark' : 'light'
            },
            responsive: [{
                breakpoint: 576,
                options: {
                    chart: {
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        new ApexCharts(chartEl, options).render();
    }

    // Enhanced Line Chart
    function initModernLineChart({
        selector,
        categories,
        series,
        colors = colorPalette
    }) {
        const chartEl = document.querySelector(selector);
        if (!chartEl || !categories.length) return;

        const options = {
            series: series,
            chart: {
                type: 'line',
                height: 280,
                background: 'transparent',
                fontFamily: 'Inter, sans-serif',
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            theme: chartTheme,
            colors: colors,
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 4,
                hover: {
                    size: 6
                }
            },
            xaxis: {
                categories: categories,
                labels: {
                    rotate: -45,
                    style: {
                        colors: chartTheme.foreColor,
                        fontSize: '11px'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: chartTheme.foreColor
                    }
                }
            },
            grid: {
                borderColor: gridColor,
                strokeDashArray: 4
            },
            legend: {
                position: 'top',
                labels: {
                    colors: chartTheme.foreColor
                }
            },
            tooltip: {
                theme: isDarkMode ? 'dark' : 'light'
            }
        };

        new ApexCharts(chartEl, options).render();
    }

    // Radial Bar Chart (for percentages)
    function initModernRadialChart({
        selector,
        labels,
        data,
        colors = colorPalette
    }) {
        const chartEl = document.querySelector(selector);
        if (!chartEl || !labels.length) return;

        const options = {
            series: data,
            chart: {
                type: 'radialBar',
                height: 280,
                background: 'transparent',
                fontFamily: 'Inter, sans-serif'
            },
            theme: chartTheme,
            colors: colors,
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: {
                            fontSize: '14px',
                            color: chartTheme.foreColor
                        },
                        value: {
                            fontSize: '20px',
                            fontWeight: 700,
                            color: chartTheme.foreColor
                        },
                        total: {
                            show: true,
                            label: 'Average',
                            color: chartTheme.foreColor,
                            formatter: () => {
                                const avg = data.reduce((a, b) => a + b, 0) / data.length;
                                return Math.round(avg) + '%';
                            }
                        }
                    },
                    track: {
                        background: isDarkMode ? '#3b4253' : '#e9ecef'
                    }
                }
            },
            labels: labels,
            legend: {
                position: 'bottom',
                labels: {
                    colors: chartTheme.foreColor
                }
            }
        };

        new ApexCharts(chartEl, options).render();
    }

    // Currency formatter
    function formatCurrency(value, currency = '₹') {
        return currency + value.toLocaleString();
    }
</script>
