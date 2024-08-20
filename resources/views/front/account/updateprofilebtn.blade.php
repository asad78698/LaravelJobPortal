<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>profile</title>
</head>

<body>


    <button id="changeprofilebtn" class="fs-6 mt-2 btn btn-primary">Change Profile Picture</button>
    <form id="profilepicform" style="margin-top: 8px; display: none" action="">
        <input type="file" name="image" id="image">
        <button class="mt-2 btn btn-primary" type="submit">Update</button>
        <p></p>
    </form>

    @section('changeprofile')
        <script>
            $("#changeprofilebtn").click(function() {
                $("#profilepicform").toggle();
            });

            $("#profilepicform").submit(function(e) {

                var formdata = new FormData(this);

                e.preventDefault();

                $.ajax({

                    url: "{{ route('account.update.profile.pic') }}",
                    type: "post",
                    dataType: "json",
                    data: formdata,
                    contentType: false,
                    processData: false,
                    success: function(response) {

                        $("#image").removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('')


                        if (response.status === false) {
                            var errors = response.errors


                            if (errors.image) {

                                $("#image").addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors.image[0])

                            } else {
                                $("#image").removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html('')
                            }

                        } else {
                            window.location.href = '{{ url()->current() }}'
                        }

                    }
                })

            })
        </script>
    @endsection
</body>

</html>
