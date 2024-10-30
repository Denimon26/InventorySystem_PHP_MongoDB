
function suggetion() {

     $('#sug_input').keyup(function(e) {

         var formData = {
             'product_name' : $('input[name=title]').val()
         };

         if(formData['product_name'].length >= 1){

           // process the form
           $.ajax({
               type        : 'POST',
               url         : 'ajax.php',
               data        : formData,
               dataType    : 'json',
               encode      : true
           })
               .done(function(data) {
                   //console.log(data);
                   $('#result').html(data).fadeIn();
                   $('#result li').click(function() {

                     $('#sug_input').val($(this).text());
                     $('#result').fadeOut(500);

                   });

                   $("#sug_input").blur(function(){
                     $("#result").fadeOut(500);
                   });

               });

         } else {

           $("#result").hide();

         };

         e.preventDefault();
     });

 }
  $('#sug-form').submit(function(e) {
      var formData = {
          'p_name' : $('input[name=title]').val()
      };
        // process the form
        $.ajax({
            type        : 'POST',
            url         : 'ajax.php',
            data        : formData,
            dataType    : 'json',
            encode      : true
        })
            .done(function(data) {
                //console.log(data);
                $('#product_info').html(data).show();
                total();
                $('.datePicker').datepicker('update', new Date());

            }).fail(function() {
                $('#product_info').html(data).show();
            });
      e.preventDefault();
  });
  function total(){
    $('#product_info input').change(function(e)  {
            var price = +$('input[name=price]').val() || 0;
            var qty   = +$('input[name=quantity]').val() || 0;
            var total = qty * price ;
                $('input[name=total]').val(total.toFixed(2));
    });
  }

  $(document).ready(function() {

    //tooltip
    $('[data-toggle="tooltip"]').tooltip();

    $('.submenu-toggle').click(function () {
       $(this).parent().children('ul.submenu').toggle(200);
    });
    //suggetion for finding product names
    suggetion();
    // Callculate total ammont
    total();

    $('.datepicker')
        .datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true
        });
  });


  function notify(type, message) {
    (() => {
      var area = document.getElementById("notification-area");
      let n = document.createElement("div");
      let notification = Math.random().toString(36).substr(2, 10);
      n.setAttribute("id", notification);
      n.classList.add("notification", type);
      n.innerHTML = "<div><b>Message</b></div>" + message;
      area.appendChild(n);

      let color = document.createElement("div");
      let colorid = "color" + Math.random().toString(36).substr(2, 10);
      color.setAttribute("id", colorid);
      color.classList.add("notification-color", type);
      document.getElementById(notification).appendChild(color);

      let icon = document.createElement("a");
      let iconid = "icon" + Math.random().toString(36).substr(2, 10);
      icon.setAttribute("id", iconid);
      icon.classList.add("notification-icon", type);
      document.getElementById(notification).appendChild(icon);

      let _icon = document.createElement("i");
      let _iconid = "_icon" + Math.random().toString(36).substr(2, 10);
      _icon.setAttribute("id", _iconid);

      if (type == 'success') {
        _icon.className = "fa fa-2x fa-check-circle";
      } else {
        _icon.className = "fa fa-2x fa-exclamation-circle";
      }
      document.getElementById(iconid).appendChild(_icon);

      area.style.display = 'block';
      setTimeout(() => {
        var notifications = document.getElementById("notification-area").getElementsByClassName("notification");
        for (let i = 0; i < notifications.length; i++) {
          if (notifications[i].getAttribute("id") == notification) {
            notifications[i].remove();
            break;
          }
        }

        if (notifications.length == 0) {
          area.style.display = 'none';
        }
      }, 5000);
    })();
  }


  let notificationCount = 0;

function sendNotification(message) {
    // Increment the notification count
    notificationCount++;
    document.getElementById('notification-count').innerText = notificationCount;

    // Optionally, display the message in a notification dropdown or alert box
    alert("New notification: " + message);
}

// Example of how to trigger a notification
document.getElementById('notification-btn').addEventListener('click', () => {
    sendNotification("You have a new message!");
});



function fetchNotifications() {
  fetch('http://localhost/InventorySystem_PHP_MongoDB/layouts/header.php')
      .then(response => response.json())
      .then(data => {
          notificationCount = data.count; // Assume API returns {count: <number>}
          document.getElementById('notification-count').innerText = notificationCount;
      });
}

// Call fetchNotifications periodically (every 1 seconds)
setInterval(fetchNotifications, 1000);
