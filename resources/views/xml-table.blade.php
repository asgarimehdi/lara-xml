<table>
    <thead>
    <tr>
        <th> modif date</th>
        <th> adddress </th>
    </tr>
    </thead>
    <tbody>
    @foreach($url['urls'] as $urls)
        <tr>
            <td> {{$urls['lastmod']}} </td>
            <td> {{$urls['loc']}} </td>
        </tr>
    @endforeach
    </tbody>
</table>