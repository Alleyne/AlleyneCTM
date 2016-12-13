@extends('templates.frontend._layouts.unify')

@section('title', '| Contacto')

@section('stylesheets')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Contactanos</h1>
            <hr>
            <form action="{{ url('contact') }}" method="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <label name="email">Email:</label>
                    <input id="email" name="email" class="form-control">
                </div>

                <div class="form-group">
                    <label name="subject">Subject:</label>
                    <input id="subject" name="subject" class="form-control">
                </div>

                <div class="form-group">
                    <label name="message">Message:</label>
                    <textarea id="message" name="message" class="form-control">Type your message here...</textarea>
                </div>

                <div class="g-recaptcha" data-sitekey="6LfPuwsUAAAAADUHG1HdmOh_p2mIi9II9a4vGTyX"></div>

                <input type="submit" value="Send Message" class="btn btn-success">
            </form>
        </div>
    </div>
@endsection