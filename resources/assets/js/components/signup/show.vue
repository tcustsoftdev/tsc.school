<template>
<div v-if="signup">
    <div  class="show-data">
        <div class="row" >
            <div  class="col-sm-3">
                <label class="label-title">姓名</label>
                <p v-text="signup.user.profile.fullname"></p>                      
            </div>
            <div class="col-sm-3">
                <label class="label-title">報名日期</label>
                <p>
                    {{ signup.date }}
                </p>                   
            </div>
            <div class="col-sm-2">
                <label class="label-title">網路報名</label>
                <p>
                    <i v-if="isTrue(signup.net)" class="fa fa-check-circle" style="color:green"></i>
                </p>    
            </div>
            <div class="col-sm-2">
                <label class="label-title">狀態</label>
                <p v-html="$options.filters.signupStatusLabel(signup.status)" >
                    {{  signup.status | signupStatusLabel }}
                </p>   
               
            </div>
            <div class="col-sm-2">
                <label class="label-title">付款方式</label>
              
                <p  v-html="getPayRecord(signup)">

                </p>
            </div>
        </div>  <!-- End row--> 
        <div class="row">
            <div class="col-sm-12">
                
                <table class="table table-striped">
                    <thead>
                        <tr style="font-size:15px">
                            <th>課程編號</th> 
                            <th>課程名稱</th> 
                            <th>課程費用</th>
                            <th>教材費用</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item,index) in signup.details" :key="index">
                            <td>{{ item.course.number }} </td>
                            <td>{{ item.course.fullName }} </td>
                            <td>{{  item.course.tuition | formatMoney }}   </td>
                            <td>{{  item.course.cost | formatMoney}}   </td>    
                           
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" >
            <div  class="col-sm-6">
                <label class="label-title">折扣</label>
                <p v-if="hasDiscount">
                    {{ signup.discount }} 
                    <span>
                        &nbsp; {{ signup.pointsText }}
                    </span>  
                </p>                      
            </div>
            <div class="col-sm-6">
                <label class="label-title">應繳金額</label>
                <p style="color:red;font-size:1.2em">
                    {{ signup.amount | formatMoney }} 
                </p>                   
            </div>
            
        </div>  <!-- End row-->      
         <div class="row" >
            <div  class="col-sm-12">
                <label class="label-title">備註</label>
                <p>
                    {{ signup.ps }} 
                </p>            
            </div>
           
            
        </div>  <!-- End row-->      

    </div>
    
    
   
</div>    
</template>

<script>
    export default {
        name: 'ShowSignup', 
        props: {
            signup: {
              type: Object,
              default: null
            },
            can_edit:{
               type: Boolean,
               default: true
            },            
            can_back:{
              type: Boolean,
              default: true
            },
            hide_delete:{
              type: Boolean,
              default: false
            },
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {

            }
        },
        computed:{
            hasDiscount(){
                return Signup.hasDiscount(this.signup);
            },
            hasReviewedBy(){
                if(!this.signup) return false;
                if(!this.signup.reviewedBy) return false;
                return true;
            }
        }, 
        methods: { 
            editReview(){
                this.$emit('edit-review')
            },
            isTrue(val){
                return Helper.isTrue(val);
            },
            getPayRecord(signup){
                let html='';
                if(signup.bill.payway) {
                    html += signup.bill.payway.name;
                    html += `<small>( ${signup.bill.payDate})</small>`;
                }
                return html;
            }
            
        }
    }
</script>
