<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Get quote | Winify Logistics</h1>

    <table>
        <tr>
            <td>First Name</td>
            <td>{{ $user_query->firstname }}</td>
        </tr>
        <tr>
            <td>Last Name</td>
            <td>{{ $user_query->lastname }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>{{ $user_query->email }}</td>
        </tr>
        <tr>
            <td>Phone</td>
            <td>{{ $user_query->phone }}</td>
        </tr>
        <tr>
            <td>State</td>
            <td>{{ $user_query->state }}</td>
        </tr>
        <tr>
            <td>Destination</td>
            <td>{{ $user_query->city }}</td>
        </tr>
        <tr>
            <td>Message</td>
            <td>{{ $user_query->message }}</td>
        </tr>
        <tr>
            <td>Form Type</td>
            <td>{{ $user_query->form_type }}</td>
        </tr>
    </table>

</body>
</html>
