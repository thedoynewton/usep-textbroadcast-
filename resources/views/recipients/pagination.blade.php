<div class="modal-body">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recipients as $recipient)
                <tr>
                    <td>{{ $recipient->fname }} {{ $recipient->lname }}</td>
                    <td>{{ $recipient->email }}</td>
                    <td>{{ $recipient->c_num }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $recipients->links() }} <!-- This generates pagination links -->
    </div>
</div>
