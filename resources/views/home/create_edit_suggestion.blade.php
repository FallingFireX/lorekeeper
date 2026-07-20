@extends('home.layout')

@section('home-title')
    New Submission
@endsection

@section('home-content')
    {!! breadcrumbs(['Suggestions' => 'suggestions', 'New Suggestion' => 'Suggestion/new']) !!}

    <h1>
        New Suggestion
    </h1>

        {!! Form::open(['url' => 'suggestions/new', 'id' => 'submissionForm']) !!}
        <div class="br-form-group alert alert-warning" style="display: none">
            <div class="form-check">
                When submitting a bug report, please use the 'URL / Title' section to briefly summarise the bug. Inlcude any links in the 'Comments' section. This is to allow an easy search.
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('title', 'Title') !!}
            {!! add_help('Enter a URL relevant to your claim (for example, a comment proving you may make this claim). This field cannot be left blank.') !!}
            {!! Form::text('title', Request::get('title'), ['class' => 'form-control', 'required']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('category_id', 'Category') !!}
            {!! Form::select('category_id', $categories, isset($suggestions->category_id) ? $suggestions->category_id : old('category_id') ?? Request::get('category_id'), ['class' => 'form-control selectize', 'placeholder' => '']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('text', 'Suggestion') !!} {!! add_help('Enter a comment for your report (no HTML). This will be viewed by the mods when reviewing your report.') !!}
            {!! Form::textarea('text', null, ['class' => 'form-control wysiwyg']) !!}
        </div>

        <div class="text-right">
            <a href="#" class="btn btn-primary" id="submitButton">Submit</a>
        </div>

        <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="modal-title h5 mb-0">Confirm Suggestion</span>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>This will submit the suggestion to the public list for everyone to view. You may edit it at any time after this.</p>
                        <div class="text-right">
                            <a href="#" id="formSubmit" class="btn btn-primary">Confirm</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var $submitButton = $('#submitButton');
            var $confirmationModal = $('#confirmationModal');
            var $formSubmit = $('#formSubmit');
            var $submissionForm = $('#submissionForm');

            $submitButton.on('click', function(e) {
                e.preventDefault();
                $confirmationModal.modal('show');
            });

            $formSubmit.on('click', function(e) {
                e.preventDefault();
                $submissionForm.submit();
            });
            $('.is-br-class').change(function(e) {
                console.log(this.checked)
                $('.br-form-group').css('display', this.checked ? 'block' : 'none')
            })
            $('.br-form-group').css('display', $('.is-br-class').prop('checked') ? 'block' : 'none')
        });
    </script>
@endsection
