const numberFormat = (value) => {
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

var options = {
    series: [{
            name: 'Attendance',
            data: attend
        },
        {
            name: 'Not Attendance',
            data: notAttend
        }
    ],
    chart: {
        type: 'bar',
        height: 393,
        stacked: true,
        toolbar: {
            show: false,
        },
        events: {
            dataPointSelection: function (event, chartContext, config) {
                console.log(chartContext, config);
                console.log(config.w.config.xaxis.categories[config.dataPointIndex]);
                $('#dept-attendance').modal('show');
                $('input[name=department_name]').val(config.w.config.xaxis.categories[config.dataPointIndex]);
                attendanceDepartment.draw();
            }
        }
    },
    colors: ['#263e8a', '#ff414d'],
    plotOptions: {
        bar: {
            horizontal: true,
        },
    },
    dataLabels: {
        enabled: true
    },
    legend: {
        show: true,
        offsetX: 90,
        offsetY: 0,
        inverseOrder: true,
        horizontalAlign: 'center',
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: '#e0e6ed',
            type: "horizontal",
            shadeIntensity: 0.5,
            gradientToColors: undefined, // optional, if not defined - uses the shades of same color in series
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 50, 100],
            colorStops: []
        }
    },
    xaxis: {
        categories: departement,
        labels: {
            show: false,
            formatter: function (val) {
                return Math.abs(Math.round(val)) + "%"
            }
        },
    },
    tooltip: {
        inverseOrder: true,
        fillSeriesColor: false
    }
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();

var optionsDonut = {
    series: data,
    chart: {
        type: 'donut',
    },
    colors: ['#263e8a', '#ec0101', '#feb019', '#A569BD'],
    dataLabels: {
        enabled: true,
        style: {
            fontSize: "8px",
            fontFamily: "Nunito, sans-serif",
            fontWeight: "bold"
        }
    },
    labels: label,
    legend: {
        position: 'bottom',
        fontSize: "11px",
        fontFamily: "Nunito, sans-serif",
        fontWeight: "bold",
        markers: {
            width: 10,
            height: 10,
        }
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: '100%'
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
};

var chartDonut = new ApexCharts(document.querySelector("#chartDonut"), optionsDonut);
chartDonut.render();

var optionsSalary = {
    chart: {
        height: 250,
        type: 'bar',
        toolbar: {
            show: false,
        },
        dropShadow: {
            enabled: true,
            top: 1,
            left: 1,
            blur: 2,
            color: '#acb0c3',
            opacity: 0.7,
        }
    },
    colors: ['#263e8a'],
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
        },
    },
    dataLabels: {
        enabled: false
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        fontSize: '14px',
        markers: {
            width: 10,
            height: 10,
        },
        itemMargin: {
            horizontal: 0,
            vertical: 8
        }
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    series: [{
        name: 'Salary',
        data: gross
    }],
    xaxis: {
        categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'October', 'November', 'December'],
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'light',
            type: 'vertical',
            shadeIntensity: 0.3,
            inverseColors: false,
            opacityFrom: 1,
            opacityTo: 0.8,
            stops: [0, 100]
        }
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return numberFormat(val)
            }
        }
    },
    yaxis: {
        show: true,
        showAlways: true,
        labels: {
            show: true,
            align: 'left',
            minWidth: 0,
            maxWidth: 160,
            style: {
                colors: [],
                fontSize: '12px',
                fontFamily: 'Nunito, sans-serif',
                fontWeight: 600,
                cssClass: 'apexcharts-yaxis-label',
            },
            offsetX: 0,
            offsetY: 0,
            rotate: 0,
            formatter: (value) => {
                return numberFormat(value)
            },
        },
    }
}

var chartSalary = new ApexCharts(document.querySelector("#chartSalary"), optionsSalary);
chartSalary.render();
