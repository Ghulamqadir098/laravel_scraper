@extends('layout.layout')

@section('content')
    <div class="container-fluid">

        <div class="row">

            @if ($product)
            @foreach ($product as $item)
            <div class=" col-6 col-md-3  col-sm-6 single__product">

                <div class="image__container feature__image m-2">
                    @foreach ($item->images as $itemimage)
                        <img class="img-fluid" width="749" height="1124" src="{{ $itemimage->image_url }}"
                            alt="Raja-Sahib-image">
                        
                    @endforeach
                    <button class=" border image__button border-dark"><a href="#">View
                        Details</a></button>
                </div>
                <h6 class="title">
                    <a href="#">{{$item->name}}</a>
                </h6>
                <h6 class="title">
                    RS {{$item->price}}
                </h6>

            </div>
        @endforeach

            @else
            <div class=" col-6 col-md-3  col-sm-6 single__product">

                <div class="image__container feature__image m-2">
                    <img class="img-fluid" width="749" height="1124"
                        src="./assets/images/AA-015-0256-9_70989fc0-2398-489b.webp" alt="Raja-Sahib-image">
                    <img class="img-fluid" width="749" height="1124" src="./assets/images/AA-015-0256-9b.webp"
                        alt="">
                    <button class=" border image__button border-dark"><a href="#">View Details</a></button>
                </div>
                <h6 class="title">
                    <a href="#">Ramsha - Wedding Collection Vol 4 - HB-401 - Unstitched</a>
                </h6>
                <h6 class="title">
                    RS 2,000
                </h6>

            </div>
            @endif
        </div>


    </div>
@endsection
