<table>
    <thead>
    <tr>
        <th> modif date</th>
        <th> adddress </th>
        <th> page_id </th>
    </tr>
    </thead>
    <tbody>
    @foreach($final_urls as $final_url)
        <tr>
            <td> {{$final_url['modification']}} </td>
            <td> {{$final_url['url']}} </td>
            <td> {{$final_url['page_id']}} </td>
        </tr>
    @endforeach
    </tbody>
</table>