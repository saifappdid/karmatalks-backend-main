<table>
    <thead>
        <tr>
            @foreach ($columns as $value)
                <th style="font-weight: bold;">{{ ucwords(str_replace('_', ' ', $value)) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr></tr>
        @foreach ($tableData as $data)
            <tr>
                @foreach ($columns as $value)
                    <td>{{ $data[$value] }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
