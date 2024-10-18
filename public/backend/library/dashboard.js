(function ($) {
    "use strict";
    var HT = {};

    HT.crateCanvas = (label, data) => {
        let canvas = document.getElementById("barChart");
        let ctx = canvas.getContext("2d");

        if (window.myBarChart) {
            window.myBarChart.destroy();
        }

        let chartData = {
            labels: label,
            datasets: [
                {
                    label: "Doanh thu",
                    backgroundColor: "rgba(26,179,148,0.5)",
                    borderColor: "rgba(26,179,148,0.7)",
                    pointBackgroundColor: "rgba(26,179,148,1)",
                    pointBorderColor: "#fff",
                    data: data
                },
            ],
        };

        let chartOption = {
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data){
                        var value = tooltipItem.yLabel;
                        value = value.toString();
                        value = value.split(/(?=(?:...)*$)/);
                        value = value.join('.');

                        return value;
                    }
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        useCallback : function (value, index, values) {
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);
                            value = value.join('.');
    
                            return value;
                        }
                    }
                }],
                xAxes : [{
                    ticks: {
                        
                    }
                }]
            }
        }

        window.myBarChart = new Chart(ctx, {type: 'bar', data: chartData, options: chartOption})
    };

    HT.changeChart = () => {
        $(document).on('click', '.chartButton', function(e){
            e.preventDefault();
            let button = $(this);
            let chartType  = button.attr('data-chart');
            $('.chartButton').removeClass('active')
            button.addClass('active')
            
            HT.callChart(chartType)          
        })
    }

    HT.callChart = (chartType) => {
        $.ajax({
            url: "http://shopprojectt.test/admin/ajax/order/chart",
            type: "GET",
            data: {
                chartType: chartType
            },
            dataType: "json",
            success: function (response) {
                HT.crateCanvas(response.label, response.data)
            },
        });
    }    


    $(document).ready(function () {
        HT.crateCanvas(label,data) 
        HT.changeChart()
    });
})(jQuery);
