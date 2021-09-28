import('../sass/app.scss')

window._ = require('lodash');
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let game = {

    init(){
        this.handleGenerateGameButton()
        this.handleSignInButton()
    },

    handleSignInButton(){
        let button = document.querySelector(`._sign_in_game`)
        if (!button) return;
        button.addEventListener(`click`, (event)=>{

            let data = this.validateSignInData(this.getSignInData())
            if (!data.success) { alert('Не вверные данные'); return; }

            axios.get(`/game/${data.id}`,{
            })
                .then((response) => {
                    console.log(response)
                })
                .catch(reason => {
                    alert('Игра с указанным ID не существует')
                })
        })
    },

    validateSignInData(data){
        if (data.id.value === ''){
            data.success = false
        }
        return {id: data.id.value, success: data.success}
    },

    getSignInData(){
        let success = true
        let id = document.querySelector(`#id`)
        if (!id) success = false
        return {id: id, success: success}
    },

    handleGenerateGameButton(){
        let button = document.querySelector(`._generate_game`)
        if (!button) return;
        button.addEventListener(`click`, (event)=>{

            let data = this.validateGenerateFormData(this.getGenerateFormData())
            if (!data.success) { alert('Не вверные данные'); return; }

            axios.post('/game',{
                width: data.width,
                height: data.height,
            })
                .then((response) => {
                    let id = document.querySelector(`._id_container`)
                    if (!id) return;

                    id.innerHTML = response.data.id
                })
                .catch(reason => {
                    alert('Ширина или высота не попадают в диапазоны минимального и максильного значения или четные')
                })
        })
    },

    getGenerateFormData(){
        let success = true
        let width = document.querySelector(`#width`)
        let height = document.querySelector(`#height`)
        if (!width || !height) success = false
        return {width: width, height: height, success: success}
    },

    validateGenerateFormData(data){
        if (data.width.value === '' || data.height.value === ''){
            data.success = false
        }
        return {width: data.width.value, height: data.height.value, success: data.success}
    }
}

game.init()


