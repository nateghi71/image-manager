<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>کتابخانه</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="module">
        $("#img").change(function (){
            $(this).parents("form").submit()
        })
        $('#form_image').on( "submit" , function (e){
            e.preventDefault();
            let formData = new FormData(this)
            $(this).trigger('reset')
                $.ajax({
                    method:"post",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url:"{{route("image.store")}}",
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache:false,
                    success:function (response){
                        let div = $("<div>" , {
                            class:'col-sm-4 col-md-3 col-xl-2 p-3',
                        });
                        let a = $("<a>" , {
                            href:'#',
                            id:'open-panel-'+response.id
                        });
                        let img = $("<img>",{
                            src:"{{url(env("IMAGE_UPLOADED_PATH"))}}" + "/" + response.name,
                            alt:"",
                            width:150,
                            height:150,
                        })

                        div.append(a);
                        a.append(img);
                        a.on('click' , clickBtn)
                        $('#img_div').append(div)
                    },
                    error:function (response){
                        console.log("error" + response)
                    },
                })
            });

        $("[id^=open-panel-]").on('click', clickBtn)

        $(".panel div:has(form)").children("form").on("submit" , function (e){
            e.preventDefault();
            $.ajax({
                method:"post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url:"{{route("image.update")}}",
                data: new FormData(this),
                dataType: 'json',
                processData: false,
                contentType: false,
                cache:false,
                success:function (response){
                    $(".panel div:has(img)").children("img").attr("src" , "{{url(env("IMAGE_UPLOADED_PATH"))}}" + "/" + response.image)
                    $(".panel div:has(img)").children("img").attr("alt" , response.description)
                    $(".panel div:has(form)").children("form").children("textarea").text(response.description)
                    console.log(response.image)

                    $("#open-panel-" + response.id).children("img").attr("alt" , response.description)
                    $("#open-panel-" + response.id).children("img").attr("src" , "{{url(env("IMAGE_UPLOADED_PATH"))}}" + "/" + response.image)

                },
                error:function (response){
                    console.log('noooooooo')
                },
            })
        })
        $(".panel div:has(hr)").children("a").on("click" , function (e){
            e.preventDefault();
            $.ajax({
                method:"post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url:$(this).attr('href'),
                dataType: 'json',
                processData: false,
                contentType: false,
                cache:false,
                success:function (response){
                    $(".panel").addClass('d-none')
                    $("#open-panel-" + response.id).remove();

                    $('#img_div').children().remove();
                    response.images.forEach(function (img){
                        let div = $("<div>" , {
                            class:'col-sm-4 col-md-3 col-xl-2 p-3',
                        });
                        let a = $("<a>" , {
                            href:'#',
                            id:'open-panel-'+img.id
                        });
                        let myimg = $("<img>",{
                            src:"{{url(env("IMAGE_UPLOADED_PATH"))}}" + "/" + img.image,
                            alt:img.description,
                            width:150,
                            height:150,
                        })

                        div.append(a);
                        a.append(myimg);
                        a.on('click' , clickBtn)
                        $('#img_div').append(div)
                    })
                },
                error:function (response){
                    console.log('noooooooo')
                },
            })
        })

        function clickBtn(event){
            event.preventDefault();
            let id = this.id.match(/\d+/)[0];
            let src = $(this).children("img").attr("src");
            let alt = $(this).children("img").attr("alt");

            $(".panel div:has(form)").children("form").trigger('reset')
            $(".panel div:has(img)").children("img").attr("src" , src)
            $(".panel div:has(img)").children("img").attr("alt" , alt)
            $(".panel div:has(form)").children("form").children("textarea").text(alt)
            $(".panel div:has(form)").children("form").children("[name=id]").val(id)

            let url = "{{route('image.destroy',['id' => ':id'])}}";
            url = url.replace(':id', id);
            $(".panel div:has(hr)").children("a").attr('href' , url);

            $(".panel").removeClass('d-none');
        }

        $("#close-panel").on('click' , function (e){
            e.preventDefault();
            $(".panel").addClass('d-none')
            $(".panel div:has(form)").children("form").trigger('reset')
        })
    </script>
</head>
<body>
    <header class="bg-black text-white py-2 ps-3 clearfix">
        <h3 class="fs-4 float-start">مدیریت عکس ها</h3>
        <form action="{{route('image.store')}}" class="float-end" id="form_image" method="post" enctype="multipart/form-data">
            @csrf
            <input type="file" name="img" id="img">
        </form>

    </header>

    <main class="main my-3 container">
        <div class="row" id="img_div">
            @foreach($images as $image)
                <div class="col-sm-4 col-md-3 col-xl-2 p-3">
                    <a href="" id="open-panel-{{$image->id}}">
                        <img src="{{url(env('IMAGE_UPLOADED_PATH').$image->image)}}" alt="{{$image->description}}" width="150" height="150">
                    </a>
                </div>
            @endforeach
        </div>
        <div class="panel bg-light row d-none">
            <a href="#" id="close-panel" class="text-danger text-decoration-none fs-5 position-absolute">X</a>
            <div class="col-5 d-flex align-items-center border-end bg-white border-light-subtle">
                <img src="" alt="" width="250" height="180" class="mx-auto">
            </div>
            <div class="col-7 d-flex flex-column">
                <form action="{{route('image.update')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <textarea name="description" class="form-control my-3 w-75 mx-auto"></textarea>
                    <input type="file" name="img" class="form-control my-3 my-3 w-75 mx-auto">
                    <button type="submit" class="btn btn-success d-block my-3 w-75 mx-auto">بروزرسانی</button>
                </form>

                <div class="mt-auto text-center">
                    <hr class="bg-black">
                    <a href="#" class="text-danger text-decoration-none d-block my-3 w-25">حذف کردن</a>
                </div>

            </div>
        </div>
    </main>

    <footer class="bg-black text-white text-center py-3 fs-6">nateghi &#169; 2023 - all right reserved</footer>
</body>
</html>
