var item514data = [];
var item511data = [];

$.getJSON("./json/itemdata.json", function (data) {
  $.getJSON('./json/item511.json', function (item511){
    $.getJSON('./json/item514.json', function (item514){

      var list = [];
      var version = ['5.11', '5.14'];
      var type = ['NORMAL', 'RANKED'];
      var url511 = 'http://ddragon.leagueoflegends.com/cdn/5.11.1/img/item/';
      var url514 = 'http://ddragon.leagueoflegends.com/cdn/5.14.1/img/item/';

      item511data = item511['data'];
      item514data = item514['data'];

      for( var item in data['data']){
        var itembox = '<div class="panel panel-default">\n';
        itembox += '<div class="panel-heading" id="'+item+'">'+item+'</div>\n';

        itembox += '<div class="panel-body">\n';
        itembox += '<div class="row">'
          itembox += '<div class="col-md-6"><div class="panel panel-default"><div class="panel-body"><h4>5.11.1</h4>\n';
          itembox += '<p>'+item511['data'][item]['name']+' - '+item511['data'][item]['gold']['total']+'g</p>\n';
          itembox += '<p><img src="'+url511+item511['data'][item]['image']['full']+'"></p>\n';
          itembox += '<p>'+item511['data'][item]['description']+'</p>\n';
          itembox += '</div></div></div>\n';
          itembox += '<div class="col-md-6"><div class="panel panel-default"><div class="panel-body"><h4>5.14.1</h4>\n';
          itembox += '<p>'+item514['data'][item]['name']+' - '+item514['data'][item]['gold']['total']+'g</p>\n';
          itembox += '<p><img src="'+url514+item514['data'][item]['image']['full']+'"></p>\n';
          itembox += '<p>'+item514['data'][item]['description']+'</p>\n';
          itembox += '</div></div></div>\n';
        itembox += '</div>\n';
        itembox += '</div>\n';

        itembox += '<table class="table">\n';
        itembox += '<thead>\n';
        itembox += '<tr><th>Patch</th><th>Queue</th><th>0~10 min</th><th>10~20 min</th><th>20~30 min</th><th>30+ min</th></tr>\n';
        itembox += '</thead>\n';

        itembox += '<tbody>\n';
        for( var vs in version){
          for(var t in type){
            itembox += '<tr>';
            itembox += '<td>'+version[vs]+'</td>\n';
            itembox += '<td>'+type[t]+'</td>\n';

            for(var key in data['data'][item][version[vs]][type[t]]){

              var total = data['data'][item][version[vs]][type[t]][key][0]+data['data'][item][version[vs]][type[t]][key][1];
              var rate = 0;
              if(total != 0) rate = data['data'][item][version[vs]][type[t]][key][1]/total*100;
              rate = rate.toFixed(2);
              var popular = total/data['total'][version[vs]][type[t]][key]*100;
              popular = popular.toFixed(2);

              itembox += '<td>winrate: '+rate+'%<br>popularity: '+popular+'% ('+total+' purchase in '+data['total'][version[vs]][type[t]]['games']+' games)</td>\n';
            }

            itembox += '</tr>\n';
          }
        }
        itembox += '</tbody>\n';

        itembox += '</table>\n';
        itembox += '</div>';

        $( ".content" ).append( itembox );
      }


    });
  });
});

$(document).ready(function(){
  $('#itemfinder').on('change', function(){
    var value = $(this).val();
    if($('#'+value).length){
      location.href = '#'+value;
    }else{
      //seach in description
      var reg = new RegExp(value, 'i');
      for(item in item511data){
        if(item511data[item]['name'].match(reg)){
          location.href = '#'+item511data[item]['id'];
          return;
        }
      }
      for(item in item514data){
        if(item514data[item]['name'].match(reg)){
          location.href = '#'+item514data[item]['id'];
          return;
        }
      }
      console.log(value+'not found');
    }
  });

  $('.back-to-top').on('click', function(event){
    event.preventDefault();
    $(document).scrollTop(0);
  });
});
