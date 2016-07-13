@extends('layouts.app')

@section('contentheader_title', 'Beacons')

@section('breadcrumbs')
<ol class="breadcrumb">
  <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
  <li class="active">Beacons</li>
</ol>
@endsection

@section('content')
<div class="row">
  <div class="col-sm-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Beacon list</h3>
        </div>
        <div class="box-body no-padding">
          <table class="table">
              <tbody>
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Name</th>
                  <th>Place</th>
                  <th>Floor</th>
                  <th class="text-right">Actions</th>
                </tr>
              @foreach($beacons as $index => $beacon)
                <tr>
                  <td>{{ $index+1 }}</td>
                  <td><a href="{{ route('beacons.show', $beacon->id) }}">{{ $beacon->name }}</a></td>
                  <td>{{ $beacon->place->name }}</td>
                  <td>{{ $beacon->map->name }}</td>
                  <td class="text-right">
                    {!! Form::open(['route' => ['beacons.destroy', $beacon->id], 'method' => 'DELETE']) !!}
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                    <a href="{{ route('beacons.edit', $beacon->id) }}" class="btn btn-primary btn-sm">Edit</a>
                    {!! Form::close() !!}
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
        </div>
      </div>
  </div>
</div>
@endsection