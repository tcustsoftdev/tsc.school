<template>

    <div>
    
        <table  class="table" style="font-size:1.2em">
            <tbody>
                <tr v-for="(notice,index) in model.viewList" :key="index">
                <td>{{ notice.date }}</td>
                <td>
                    <a @click.prevent="onSelected(notice.id)" >
                        {{ notice.title }}
                    </a>
                </td>
                </tr>
            
            </tbody>
            
        </table>
        <slot name="table-footer"> 
        
        </slot> 
        
    </div>
</template>

<script>
   
    export default {
        name:'NoticeTable',
        props: {
            model:{
                type: Object,
                default: null
            },
        },
        beforeMount(){
          
        },
        data(){
            return{
               
            }
        },
        computed: {
            
        },
        methods:{
            init(){
               
            },
            onSelected(id){
                this.$emit('selected',id);
            }
        },
        
    }
</script>
