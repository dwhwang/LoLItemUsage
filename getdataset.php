<!DOCTYPE html>
<html>
  <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  </head>
  <body>
    <select id="dataset">
      <option></option>
      <?php
        $scanned_directory = array_diff(scandir('./dataset'), array('..', '.'));
        foreach($scanned_directory as $file){
          echo '<option>'.$file.'</option>';
        }
      ?>
    </select>
    <select id="region">
      <option>br</option>
      <option>eune</option>
      <option>euw</option>
      <option>kr</option>
      <option>lan</option>
      <option>las</option>
      <option selected >na</option>
      <option>oce</option>
      <option>ru</option>
      <option>tr</option>
    </select>
    <button id="ok">Start</button>
    <button id="clear">Clear log</button>
    <div id="progress"></div>
    <div id="log">
    </div>
    <script>
      $('#ok').on('click', function(){
        var ds = $('#dataset').val();
        var region = $('#region').val();
        if(ds !== '') {
          $.ajax({
            url: './lib/getMatchData.php?region='+region+'&dataset='+ds,
            method: 'GET',
            error: function(jqXHR, status, err){
              $('#log').append('Error '+status+' '+err+'<br >');
            },
            success: function(data){
              $('#log').append(data+'<br >');
            }
          });
        }else{
          $('#log').append('Please select a data set <br >');
        }
      });

      $('#clear').on('click', function(){
        $('#log').empty();
      });
    </script>
  </body>
</html>
