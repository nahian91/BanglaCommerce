jQuery(document).ready(function($){
    // Date picker
    $('.date-picker').datepicker({ dateFormat: 'yy-mm-dd' });

    // Add/Remove Time Slot
    $('#add-slot').click(function(e){ e.preventDefault();
        $('#bes-time-slots').append('<div class="bes-repeater-item"><input type="text" name="bes_delivery_scheduler[time_slots][]" placeholder="e.g. 9 AM - 12 PM"><button class="button remove-item">Remove</button></div>');
    });
    $('#reset-slot').click(function(e){ e.preventDefault();
        let defaults = ['9 AM - 12 PM','12 PM - 3 PM','3 PM - 6 PM','6 PM - 9 PM','9 PM - 11 PM'];
        $('#bes-time-slots').html('');
        defaults.forEach(function(s){ $('#bes-time-slots').append('<div class="bes-repeater-item"><input type="text" name="bes_delivery_scheduler[time_slots][]" value="'+s+'"><button class="button remove-item">Remove</button></div>'); });
    });

    // Add/Remove Blackout Date
    $('#add-date').click(function(e){ e.preventDefault();
        $('#bes-blackout-dates').append('<div class="bes-repeater-item"><input type="text" class="date-picker" name="bes_delivery_scheduler[blackout_dates][]" placeholder="YYYY-MM-DD"><button class="button remove-item">Remove</button></div>');
        $('.date-picker').datepicker({ dateFormat: 'yy-mm-dd' });
    });
    $('#reset-date').click(function(e){ e.preventDefault();
        let today = new Date(); let dates=[];
        for(let i=1;i<=5;i++){ let d=new Date(); d.setDate(today.getDate()+i); dates.push(d.toISOString().split('T')[0]); }
        $('#bes-blackout-dates').html('');
        dates.forEach(function(d){ $('#bes-blackout-dates').append('<div class="bes-repeater-item"><input type="text" class="date-picker" name="bes_delivery_scheduler[blackout_dates][]" value="'+d+'"><button class="button remove-item">Remove</button></div>'); });
        $('.date-picker').datepicker({ dateFormat: 'yy-mm-dd' });
    });

    // Remove any item
    $(document).on('click','.remove-item',function(e){ e.preventDefault(); $(this).parent().remove(); });
});
