<a href="{{ url('manufacture/point/machine') }}" class="btn {{\Request::segment(4)==''?'btn-primary':'btn-info'}}">
    List data
</a>
<a href="{{ url('manufacture/point/machine/create') }}"
   class="btn {{\Request::segment(4)=='create'?'btn-primary':'btn-info'}}">
    Create
</a>
<a href="{{ url('temporary-access/manufacture | machine/point manufacture machine') }}" class="btn btn-info">
    Temporary Access
</a>
<br/><br/> 
