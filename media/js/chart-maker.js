/**
 * Pie Generator after ajax call
 * newData from ajax response
 */
function cpm_poll_ajax_pie_maker(newData,from,cpmPollId) {
     jQuery('#chart-area-'+cpmPollId+'-'+from).replaceWith('<canvas id="chart-area-'+cpmPollId+'-'+from+'"></canvas>');
    var ctx =jQuery('#chart-area-'+cpmPollId+'-'+from).get(0).getContext("2d");
    var newChartOption = {
                        responsive : true,
                        animation : true,
                        animationEasing: "easeOutQuart",
                        showScale: true,
                        legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
                        tooltipTemplate: "<%=label%>: <%= numeral(circumference / 6.283).format('(0[.][00]%)') %>"
                    }
    jQuery('#voting-result-'+cpmPollId+'-'+from).resize();
    window.newMyPie = new Chart(ctx).Pie(newData, newChartOption);
}

/**
 * Doughnut Generator after ajax call
 * newData from ajax response
 */
function cpm_poll_ajax_doughnut_maker(newData,from,cpmPollId) {
    jQuery('#chart-area-'+cpmPollId+'-'+from).replaceWith('<canvas id="chart-area-'+cpmPollId+'-'+from+'"></canvas>');
    var ctx =jQuery('#chart-area-'+cpmPollId+'-'+from).get(0).getContext("2d");
    var newChartOption = {
                        responsive : true,
                        animation : true,
                        animationEasing: "easeOutQuart",
                        showScale: true,
                        legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
                        tooltipTemplate: "<%=label%>: <%= numeral(circumference / 6.283).format('(0[.][00]%)') %>"
                    }
    jQuery('#voting-result-'+cpmPollId+'-'+from).resize();
    window.newMyDoughnut = new Chart(ctx).Doughnut(newData, newChartOption);
}
/**
 * Polar Chart Generator after ajax call
 * newData from ajax response
 */
function cpm_poll_ajax_polar_maker(newData,from,cpmPollId) {
     jQuery('#chart-area-'+cpmPollId+'-'+from).replaceWith('<canvas id="chart-area-'+cpmPollId+'-'+from+'"></canvas>');
    var ctx =jQuery('#chart-area-'+cpmPollId+'-'+from).get(0).getContext("2d");
    var newChartOption = {
                        responsive : true,
                        animation : true,
                        animationEasing: "easeOutQuart",
                        showScale: true,
                        legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
                        tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>"
                    }
    jQuery('#voting-result-'+cpmPollId+'-'+from).resize();
    window.newMyPolar = new Chart(ctx).PolarArea(newData, newChartOption);
}
/**
 * Bar Grpah Generator after ajax call
 * newData from ajax response
 */
function cpm_poll_ajax_bar_maker(newData,from,cpmPollId) {
    var barData = newData.actual_data;
    var barLables = jQuery.parseJSON(newData.lables);
    var barFillColors = newData.bar_colors;
    var barChartData = {
        labels: barLables,
        datasets: [
            {
                label: "",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,0.8)",
                highlightStroke: "rgba(220,220,220,1)",
                data: barData
            }
        ]
    };
    var newChartOption = {
                        responsive : true,
                        animation : true,
                        animationEasing: "easeOutQuart",
                        showScale: true
                    }
    jQuery('#chart-area-'+cpmPollId+'-'+from).replaceWith('<canvas id="chart-area-'+cpmPollId+'-'+from+'"></canvas>');
     var ctx =jQuery('#chart-area-'+cpmPollId+'-'+from).get(0).getContext("2d");
    window.myObjBar = new Chart(ctx).Bar(barChartData, newChartOption);
    jQuery.each(myObjBar.datasets[0].bars, function(index, val) {
       val.fillColor = barFillColors[index];
       val.highlightFill = barFillColors[index];
    });
    myObjBar.update();
    jQuery('#voting-result-'+cpmPollId+'-'+from).resize();
}
/**
 * Line Grpah Generator after ajax call
 * newData from ajax response
 */
function cpm_poll_ajax_line_maker(newData,from,cpmPollId) {
    var lineData = newData.actual_data;
    var lineLables = jQuery.parseJSON(newData.lables);
    var lineFillColors = newData.bar_colors;
    var newLineData = {
        labels: lineLables,
        datasets: [
            {
                label: " ",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,0.8)",
                highlightStroke: "rgba(220,220,220,1)",
                data: lineData
            }
        ]
    };
    var newChartOption = {
                        responsive : true,
                        animation : true,
                        animationEasing: "easeOutQuart",
                        showScale: true
                    }
    jQuery('#chart-area-'+cpmPollId+'-'+from).replaceWith('<canvas id="chart-area-'+cpmPollId+'-'+from+'"></canvas>');
     var ctx =jQuery('#chart-area-'+cpmPollId+'-'+from).get(0).getContext("2d");
    window.myObjLine = new Chart(ctx).Line(newLineData, newChartOption);
    myObjLine.update();
    jQuery('#voting-result-'+cpmPollId+'-'+from).resize();
}
/**
 * Radar Grpah Generator after ajax call
 * newData from ajax response
 */
function cpm_poll_ajax_radar_maker(newData,from,cpmPollId) {
    var radarData = newData.actual_data;
    var radarLables = jQuery.parseJSON(newData.lables);
    var lineFillColors = newData.bar_colors;
    var newRadarData = {
        labels: radarLables,
        datasets: [
            {
                label: " ",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,0.8)",
                highlightStroke: "rgba(220,220,220,1)",
                data: radarData
            }
        ]
    };
    var newChartOption = {
                        responsive : true,
                        animation : true,
                        animationEasing: "easeOutQuart",
                        showScale: true
                    }
    jQuery('#chart-area-'+cpmPollId+'-'+from).replaceWith('<canvas id="chart-area-'+cpmPollId+'-'+from+'"></canvas>');
     var ctx =jQuery('#chart-area-'+cpmPollId+'-'+from).get(0).getContext("2d");
    window.myObjRadar = new Chart(ctx).Radar(newRadarData, newChartOption);
    myObjRadar.update();
    jQuery('#voting-result-'+cpmPollId+'-'+from).resize();
}

