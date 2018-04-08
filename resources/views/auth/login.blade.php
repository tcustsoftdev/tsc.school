@extends('layouts.client')

@section('content')

<login-view :intend="intend"></login-view>

@endsection


@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            intend:'',
        }
    },
    beforeMount() {
        this.intend = {!! json_encode($intend) !!} ;
               
    },
    mounted(){
        onPageLoaded();
    },
    methods: {
        
    }

});



</script>

@endsection
