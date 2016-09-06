function sendPost ($url,$model,$banco,$campos,$action,$key,$id) {

$div = '#'+$id;

$.post( $url, {
model:$model,
banco : $banco,
campos :$campos,
action:$action,
key:$key
 },function( data ) {

  $($div).html('');
  $($div).html(data);

  });

};

function runPtimer($url,$model,$banco,$campos,$action,$key,$time,$id){
setInterval(function() {
     sendPost($url,$model,$banco,$campos,$action,$key,$id);

},$time);

}
