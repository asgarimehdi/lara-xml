<table>
    <thead>
    <tr>
        <th> modif date</th>
        <th> adddress </th>
        <th> url_decoded </th>
    </tr>
    </thead>
    <tbody>
    @foreach($final_urls as $final_url)
        <tr>
            <td> {{$final_url['modification']}} </td>
            <td> {{$final_url['url']}} </td>
            <td> {{$final_url['url_decoded']}} </td>
        </tr>
    @endforeach
    </tbody>
</table>