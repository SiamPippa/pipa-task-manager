@props(['model', 'routePrefix'])

@can('view', $model)
  <a href="{{ route($routePrefix.'.show', $model) }}" class="btn btn-sm btn-info">View</a>
@endcan
@can('update', $model)
  <a href="{{ route($routePrefix.'.edit', $model) }}" class="btn btn-sm btn-warning">Edit</a>
@endcan
@can('delete', $model)
  <form action="{{ route($routePrefix.'.destroy', $model) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?')">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
  </form>
@endcan
