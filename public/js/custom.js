/**
 * Created with JetBrains PhpStorm.
 * User: Rodger
 * Date: 8/18/13
 * Time: 10:57 PM
 * To change this template use File | Settings | File Templates.
 */
jQuery(function($) {

    $('#gameboard').on('click', '.move', function(e){
        e.preventDefault();
        // GET THIS POSITION ID
        var position_id = $(this).attr('id');
        $.ajax({
           url: '/tic-tac-toe/move',
            type: 'POST',
            dataType: 'json',
            data: {position: position_id},
            success: function(response){
                if(response.success == true){
                    // the chosen square is marked with this players mark
                    var mark = response.mark;
                    var time = response.timestamp;
                    var game_id = response.game_id;
                    // the listener is engaged
                    listen(time, game_id);
                }
            }
        });
    });


    function listen(time, game_id){
        var myInterval = setInterval( function(){

            $.ajax({
                url: '/tic-tac-toe/update',
                type: 'POST',
                dataType: 'json',
                // WE NEED TO SEND THE TIMESTAMP OF THE MARK WE JUST CREATED
                data: {'timestamp' : time, 'game_id' : $game_id},
                success: function(response){

                    if(response.success == true){
                        // WE NEED TO KNOW WHAT THE NEW POSITION IS THAT WAS CHOSEN BY THE OPPONENET
                        // AND WHAT THAT PLAYER'S MARK IS

                        // ONCE THE NEW DATA HAS BEEN OBTAINED, END setInterval
                        clearInterval(myInterval);
                    }

                }
            });
            // REPEATS EVERY 5 SECONDS UNTIL clearInterval IS CALLED
        },5000);
    }


});