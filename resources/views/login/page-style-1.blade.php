@extends('layouts.app')

@section('header')
    {{--    @include('partials.sections.header')--}}
@endsection

@section('content')
    <main class="main">
        @if(!empty(get_the_content()))
            @include('partials.content.content-page')
        @else
            <?php while (have_posts()) : the_post(); ?>
            @php(the_content())
            <?php endwhile; ?>
        @endif
    </main>
@endsection

@section('footer')
    {{--    @include('partials.sections.footer')--}}
@endsection
