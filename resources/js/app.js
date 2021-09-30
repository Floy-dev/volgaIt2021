import('../sass/app.scss')

window._ = require('lodash');
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let game = {

    last_color: null,

    init(){
        this.handleGenerateGameButton()
        this.handleSignInButton()
        this.handleMoveGame()
    },

    handleMoveGame(){
        let buttons = document.querySelectorAll(`._put_color`)
        if (buttons.length < 1) return;
        buttons.forEach(button =>{
            button.addEventListener(`click`, async (event) => {

                let playerId = null
                await axios.get(`/game/${button.dataset.gameId}`)
                    .then((response) => {
                        playerId = response.data.currentPlayerId
                    })

                if (!playerId) {
                    alert('Игрок не найден');
                    return;
                }

                axios.put(`/game/${button.dataset.gameId}`, {
                    gameId: button.dataset.gameId,
                    playerId: playerId,
                    color: button.dataset.color,
                })
                    .then((response) => {
                        this.redrawField({gameId: button.dataset.gameId, color: button.dataset.color})
                    })
                    .catch(reason => {
                        alert(reason.response.data)
                    })
            })
        })
    },

    async redrawField(data){
        let view = null
        await axios.post(`/room/cells/${data.gameId}`)
            .then((response) => {
                view = response.data.view
            })
        let container = document.querySelector(`._cells_container`)
        if (!container || !view) return
        container.innerHTML = view

        if (this.last_color){
            let button = document.querySelector(`.room__button[data-color="${this.last_color}"]`)
            if (button){
                button.classList.remove('hide')
            }
        }

        this.last_color = data.color
        let button = document.querySelector(`.room__button[data-color="${this.last_color}"]`)
        if (button){
            button.classList.add('hide')
        }
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
                    window.location.href = `/room/${response.data.id}`
                })
                .catch(reason => {
                    alert(reason.response.data)
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


