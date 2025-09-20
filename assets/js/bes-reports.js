jQuery(document).ready(function($){
    if(typeof besReportsData === 'undefined') return;

    const districtLabels = besReportsData.districts.map(d=>d.district);
    const districtData   = besReportsData.districts.map(d=>parseInt(d.orders_count));

    const paymentLabels  = besReportsData.payments.map(p=>p.payment_method);
    const paymentData    = besReportsData.payments.map(p=>parseFloat(p.avg_order));

    const slotLabels     = besReportsData.slots.map(s=>s.slot);
    const slotData       = besReportsData.slots.map(s=>parseInt(s.orders_count));

    const customerLabels = besReportsData.customers.map(c=>c.customer_type);
    const customerData   = besReportsData.customers.map(c=>parseInt(c.total));

    const createChart = (ctx, type, labels, data, bgColor) => new Chart(ctx, {
        type:type,
        data:{labels:labels,datasets:[{label:'',data:data,backgroundColor:bgColor}]},
        options:{responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}
    });

    createChart(document.getElementById('districtChart').getContext('2d'),'bar',districtLabels,districtData,'rgba(75,192,192,0.6)');
    createChart(document.getElementById('paymentChart').getContext('2d'),'bar',paymentLabels,paymentData,'rgba(153,102,255,0.6)');
    createChart(document.getElementById('slotChart').getContext('2d'),'bar',slotLabels,slotData,'rgba(255,159,64,0.6)');

    new Chart(document.getElementById('customerChart').getContext('2d'),{
        type:'pie',
        data:{
            labels:customerLabels,
            datasets:[{label:'Orders', data:customerData, backgroundColor:['#36A2EB','#FF6384']}]
        },
        options:{responsive:true}
    });
});
