$(document).ready(function() {
    var formSubmitted = false;
    //Autorization block
    var $loginInForm = $('#login-in-form');
    var $registerForm = $('#register-form');
    $('#registration-link').on('click',function () {formSwich($loginInForm,$registerForm);});
    $('#login-link').on('click',function () {formSwich($registerForm,$loginInForm);});
    function formSwich($oldForm,$newForm) {
        $oldForm.fadeToggle(500);
        setTimeout(function(){
            $newForm.fadeToggle(500);},500);
    }
    var $signInForm = $('#sign-in-form'),
        $emailInput = $signInForm.find('#recipient-email'),
        $passwordInput = $signInForm.find('#recipient-password'),
        $passwordCheckInput = $signInForm.find('#recipient-password');
    $("form").submit(function (event) {
        switch(this.id) {
            case "login-in-form":
                event.preventDefault();
                $.post('/login', {
                    userName: $('#login-name').val(),
                    userPassword: $('#login-password').val()
                },'json')
                    .done(function (r) {
                        window.location.replace("/");
                        console.log('Success',r);
                    })
                    .fail(function (r) {
                        console.log('Fail',r.responseJSON.message);
                    });
                return false;
                break;
            case "register-form":
                event.preventDefault();
                $.post('/registration', {
                    userName: $('#register-name').val(),
                    userPassword: $('#register-password').val(),
                    userCheckPassword: $('#register-check-password').val()
                },'json')
                    .done(function (r) {
                        window.location.replace("/");
                        console.log('Success',r);
                    })
                    .fail(function (r) {
                        console.log('Fail',r.responseJSON.message);
                    });
                return false;
                break;
                }
    });

    $signInForm.on('submit', function(event) {
        event.preventDefault();
        $.post('/signin', { userEmail: $emailInput.val(), userPassword: $passwordInput.val()},'json')
            .done(function (r) {
                console.log('Success',r);
            })
            .fail(function (r) {
                console.log('Fail',r.responseJSON.message);
            })
    });
    //Autoriztion block end
    var $canvas = $("#canvas");
    $canvas.click(function(event){
        var rect = canvas.getBoundingClientRect();
        var x = event.clientX - rect.left;
        var y = event.clientY - rect.top;
        var cell = getCell(x,y,fildSize);
        $.post('/api/click', {
            col: cell[0],
            row: cell[1]},
            'json')
            .done(function (r) {
                var sign = r.message.sign;
                console.log('Success',r);
                if (sign){
                    drawCell(cell[0],cell[1],fildSize,sign);
                }
                // console.log('Success',r);
            })
            .fail(function (r) {
                console.log('Fail',r.responseJSON.message);
            });

    });
    if ($canvas.length){
        var fildSize = 3;
        var c = document.getElementById("canvas");
        var width = c.width;
        var height = c.height;
        var cellWidth =  width/fildSize;
        var cellHeight =  height/fildSize;
        drawField(fildSize);
        doPoll();
        function doPoll(){
            $.post('/api/setonline', {
                   userId: $('#player').data('id')},
                'json')
                .done(function (r) {
                    $('#opName').text(r.message.op_name);
                    var field = r.message.field;
                    var result = r.message.result;
                    var alertBlock = $('#alert');
                    if(field){
                        updateField(field)
                    }

                    if(result){
                        if(result != 1){
                            victoryLine(result);
                            alert(r.message.message);
                            }else alert('Ничья');
                        setTimeout(window.location.replace("/"),1000);

                    } else {
                        alertBlock.text(r.message.message);
                    }
                    setTimeout(doPoll,1000);
                })
                .fail(function (r) {
                    console.log('Fail',r.responseJSON.message);
                });
            }
    }
    
    function victoryLine(result) {
        var c = document.getElementById("canvas");
        var ctx = c.getContext("2d");
        ctx.strokeStyle="#ff0000";
        ctx.lineWidth=8;
        if (result.lineType == 'h'){
            ctx.beginPath();
            ctx.moveTo(0,cellHeight*(result.line+1)-cellHeight/2);
            ctx.lineTo(cellWidth*fildSize,cellHeight*(result.line+1)-cellHeight/2);
            ctx.stroke();
        }
        if (result.lineType == 'v'){
            ctx.beginPath();
            ctx.moveTo(cellWidth*(result.line+1)-cellWidth/2,0);
            ctx.lineTo(cellWidth*(result.line+1)-cellWidth/2,cellHeight*fildSize);
            ctx.stroke();
        }
        if (result.lineType == 'd' && result.line == 1){
            ctx.beginPath();
            ctx.moveTo(0,0);
            ctx.lineTo(cellWidth*fildSize,cellHeight*fildSize);
            ctx.stroke();
        }
        if (result.lineType == 'd' && result.line == 2){
            ctx.beginPath();
            ctx.moveTo(cellWidth*fildSize,0);
            ctx.lineTo(0,cellHeight*fildSize);
            ctx.stroke();
        }
    }

    function updateField(field) {
        var c = document.getElementById("canvas");
        var ctx = c.getContext("2d");
        ctx.clearRect(0, 0, c.width, c.height);
        drawField(fildSize);
        for(var i=0;i<3;i++)
        {
            for(var j=0;j<3;j++){
                if(field[i][j] != 0){
                    drawCell(j,i,fildSize,field[i][j]);
                }
            }
        }
    }

    function drawField(size){
        var c = document.getElementById("canvas");
        var ctx = c.getContext("2d");
        ctx.strokeStyle="#000000";
        ctx.lineWidth=1;
        var tempWidth =  width/size;
        var tempHeight =  height/size;
        for (var i = 0; i <= size; i++){
            ctx.beginPath();
            ctx.moveTo(i*tempWidth,0);
            ctx.lineTo(i*tempWidth,height);
            ctx.stroke();

            ctx.beginPath();
            ctx.moveTo(0,i*tempHeight);
            ctx.lineTo(width,i*tempHeight);
            ctx.stroke();
        }
    }
    function getCell(x,y,size) {
        var c = document.getElementById("canvas");
        var row = Math.floor(y/cellHeight);
        var col = Math.floor(x/cellWidth);
        return [col,row];
    }
    function drawCell(col,row,size,stroke) {
        var c = document.getElementById("canvas");
        x0 = col * cellWidth + 5;
        x1 = (col + 1) * cellWidth - 5;
        y0 = row * cellHeight + 5;
        y1 = (row + 1)* cellHeight - 5;
        if (stroke == 2){
            drawCircle(x0,x1,y0,y1);
        }else {
            drawCross(x0,x1,y0,y1);
        }
    }
    function drawCross(x0,x1,y0,y1) {
        var c = document.getElementById("canvas");
        var ctx = c.getContext("2d");
        ctx.lineWidth=5;
        ctx.strokeStyle="#0000FF";
        ctx.beginPath();
        ctx.moveTo(x0,y0);
        ctx.lineTo(x1,y1);
        ctx.stroke();

        ctx.beginPath();
        ctx.moveTo(x1,y0);
        ctx.lineTo(x0,y1);
        ctx.stroke();
    }
    function drawCircle(x0,x1,y0,y1) {
        var c = document.getElementById("canvas");
        var ctx = c.getContext("2d");
        ctx.lineWidth=5;
        ctx.strokeStyle="#FF0000";
        ctx.beginPath();
        ctx.arc((x1+x0)/2, (y1+y0)/2, (x1-x0)/2, 0, Math.PI * 2, true);
        ctx.stroke();
    }

});
