class Student {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/manage/students';
    }
    static showUrl(id){
        return `${this.source()}/${id}`;
    }
    static createUrl() {
        return this.source() + '/create';
    }
    static storeUrl() {
        return this.source();
    }
    static editUrl(id) {
        return `${this.source()}/${id}/edit`;
    }
    static updateUrl(id) {
        return this.source() + `/${id}`;
    }
    static deleteUrl(id){
        return this.source() + `/${id}`;
    }
   

    static show(id) {
        return new Promise((resolve, reject) => {
            let url = this.showUrl(id)
            axios.get(url)
                .then(response => {
                    resolve(response.data)
                })
                .catch(error => {
                    reject(error);
                })

        })
    }

   
 
    static index(params){
        let url = this.source();
        url=Helper.buildQuery(url, params);
 
 
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                        resolve(response.data);
                })
                .catch(error => {
                        reject(error);
                })
 
        })
    }
     
    static create() {
        let url = this.createUrl();
 
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                        resolve(response.data);
                })
                .catch(error => {
                        reject(error);
                })
 
        })
    }
     
    static store(form){
        let url = this.storeUrl();
        let method = 'post';
        return new Promise((resolve, reject) => {
            form.submit(method, url)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
        })
    }
 
    static edit(id) {
        let url = this.editUrl(id);

        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                        resolve(response.data);
                })
                .catch(error => {
                        reject(error);
                })

        })
    }
 
    static update(id,form){
        let url = this.updateUrl(id);
        let method = 'put';
        return new Promise((resolve, reject) => {
            form.submit(method, url)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
        })
    }

    static updateScores(students) {
        let form = new Form({
            students: students
        })
        return new Promise((resolve, reject) => {

            let url = this.source() + '/scores/update'
            form.post(url)
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    reject(error);
                })
        })

    }
 
 
    static remove(id) {
        let url = this.deleteUrl(id);
        
        return new Promise((resolve, reject) => {
            axios.delete(url)
                .then(response => {
                        resolve(response.data);
                })
                .catch(error => {
                        reject(error);
                })

        })
    }

    static getStatusText(status){
        status=parseInt(status);
       
        if(status==1) return '正常';
        if(status==-1) return '已退出';
        
    }
    static getStatusStyle(status){
        status=parseInt(status);
        if(status==-1) return 'default';
        if(status==1) return 'info';

        return ''
    }

    static statusLabel(status){
        let text=this.getStatusText(status)
        let style='label label-' + this.getStatusStyle(status)
        
        return `<span class="${style}" > ${text} </span>`
    }

    // static getByUser(user){
       
    //     let url = this.source() + `/GetByUser/${user}`;

    //     return new Promise((resolve, reject) => {
    //         axios.get(url)
    //             .then(response => {
    //                     resolve(response.data);
    //             })
    //             .catch(error => {
    //                     reject(error);
    //             })

    //     })
    // }
    
    
 
    
 }
 
 
 export default Student;