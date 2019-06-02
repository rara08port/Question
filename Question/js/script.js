$(function () {

    //テキストカウント
    var $countUp = $('#js-countup');
    var $countView = $('#js-countup-view');
    $countUp.on('keyup', function (e) {
        $countView.text($(this).val().length); 
    });


    // 画像ライブプレビュー

    var $dropArea = $('.js-area-drop');
    var $fileInput = $('.input-file');
    $dropArea.on('dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', '3px #ccc dashed');
    });
    $dropArea.on('dragleave', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', 'none');
    });
    $fileInput.on('change', function (e) {
        $dropArea.css('border', 'none');
        var file = this.files[0], //files[0]name,files[1]type,files[2]size,           
            $img = $(this).siblings('.prev-img'), //img要素
            fileReader = new FileReader();

        fileReader.onload = function (event) {
            // 読み込んだデータをimgに設定
            $img.attr('src', event.target.result).show();
        };
        
        fileReader.readAsDataURL(file);
    });
    


    // お気に入り登録・削除
    var $good;
    var goodId;
    $good = $('.js-click-like');
    goodId = $good.data('goodid');
    $good.on('click', function () {
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: "ajaxLike.php",
            data: { goodbtn: goodId }
        }).done(function (data) {
            $this.toggleClass('active');

        }).fail(function (data) {

        });
    });
    

    // レスポンス用ユーザーメニュー
    
    var $menu_btn = $('.js-menu-slide'),
        $menulist = $('#js-menulist');

    $menu_btn.on('click', function () {
        $menulist.toggleClass('right-slide');
    });
    


   

});