<template>
    <table  class="table">
        <thead>
            <tr>
                <th style="width:25%">
                    課程
                </th>
                <th style="width:25%">
                    退費比例
                </th>
                <th v-if="!can_edit" style="width:10%">
                    應退學費
                </th>
                <th>
                    備註
                </th>
                <th v-if="can_edit" style="width:10%">

                </th>
                
            </tr>
        </thead>
        <tbody>
            
            <row v-for="(item,index) in quit_details" :key="index"
                :can_edit="can_edit"
                :model="item" :percents_options="percents_options"
                :edit="editting_id==item.signupDetailId"  
                @edit="beginEdit(item)" @cancel="cancelEdit"  
                @submit="cancelEdit" >
            </row>
        </tbody>
    
    </table>
</template>


<script>
import Row from './detail-row.vue';
export default {
    name:'QuitDetails',
    props: {
        quit_details:{
            type: Array,
            default: null
        },
        can_edit:{
            type: Boolean,
            default: true
        },
        percents_options:{
            type: Array,
            default: null
        },
        editting_id:{
            type: Number,
            default: 0
        }
	},
    components: {
        Row
    },
    data(){
		return {
           
			
		}
	},
    computed:{
		
	},
    beforeMount() {
		this.init();
	},
    methods:{
		init(){
			

        },
        cancelEdit(){
			this.$emit('cancel-edit');
		},
        beginEdit(item) {
            this.$emit('edit',item);
        }
	}
}
</script>
