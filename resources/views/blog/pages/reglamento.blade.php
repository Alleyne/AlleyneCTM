@extends('templates.frontend._layouts.unify')

@section('title', '| Reglamento')

@section('content')
    <!--=== Content Part ===-->
    <div class="container content">
        <div class="shadow-wrapper">
            <blockquote class="hero box-shadow shadow-effect-2">
                <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing duis mollis, est non commodo luctus elit posuere erat a ante. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis lorem ipsum dolor sit amet, consectetur adipiscing"</em></p>
                <small><em>Reglamentos del Ph Woodland</em></small></br>
                
                <a href="{{ asset('/assets/reglamento/Reglamento de Copropiedad.pdf') }}" target="_blank"><i class="fa fa-book"></i> Reglamento de Copropiedad</a></br>
                <a href="{{ asset('/assets/reglamento/Modificacion Reglamento de Copropiedad.pdf') }}" target="_blank"><i class="fa fa-book"></i> Modificacion Reglamento de Copropiedad.pdf</a> 
            
            </blockquote>
        </div>

        {{-- <div class="margin-bottom-60"></div> --}}
    </div>
@endsection