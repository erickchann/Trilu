<template>
    <div>
        <Nav></Nav>

        <div class="container">
            <div class="board-container">
                <div class="board-wrapper" v-for="board in boards" :key="board.id" >
                    <div class="board" @click.self="show(board.id)">
                        <p v-if="editBoard != board.id" @click="editBoard = board.id, newName = board.name">{{ board.name }}</p>
                        <input type="text" placeholder="Are you sure want to delete?" autofocus v-if="editBoard == board.id" @blur="editBoard = 0" v-model="newName" @keyup.enter="update">
                    </div>
                    
                </div>
                
                <div class="board-wrapper">
                    <div class="board new-board" @click="createBoard = true">
                        <p v-if="!createBoard">Create new board...</p>
                        <input type="text" placeholder="New Board Name" autofocus v-if="createBoard" @blur="createBoard = false" @keyup.enter="create" v-model="boardName">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    components: {
        Nav: () => import('@/components/Nav.vue')
    },
    data() {
        return {
            token: localStorage.getItem('token'),
            createBoard: false,
            editBoard: 0,
            newName: '',
            boardName: '',
            boards: ''
        }
    },
    methods: {
        getBoards() {
            axios.get(`board?token=${this.token}`)
                .then(res => {
                    this.boards = res.data;
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
        },
        create() {
            axios.post(`board?token=${this.token}`, {name: this.boardName})
                .then(res => {
                    console.log(res.data.message);
                    this.getBoards();
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
        },
        update() {
            if (this.newName) {
                axios.put(`board/${this.editBoard}?token=${this.token}`, {name: this.newName})
                    .then(res => {
                        console.log(res.data.message);
                        this.getBoards();
                    })
                    .catch(err => {
                        console.log(err.response.data.message);
                    });
            } else {
                axios.delete(`board/${this.editBoard}?token=${this.token}`)
                    .then(res => {
                        console.log(res.data.message);
                        this.getBoards();
                    })
                    .catch(err => {
                        console.log(err.response.data.message);
                    });
            }
        },
        show(id) {
            this.$router.push(`/board/${id}`);
        }
    },
    created() {
        this.getBoards();
    }
}
</script>

<style scoped>
.container{
	display: flex;
	flex-direction: row;
	align-items: flex-start;
	justify-content: center;
}

.board-container{
	flex: 1 1 100%;
	margin: 20px;
	display: flex;
	flex-flow: row wrap;
    justify-content: space-around;
    align-items: flex-start;
}

.board-container::after {
  content: "";
  flex: auto;
}

.board-wrapper{
	display:flex;
	flex: 0 0 25%;
	box-sizing: border-box;
	padding: 0.5em;
	cursor: pointer;
}

.board{
	flex: 1;
	padding: 1em;
	background: #248ea9;
	color: #ffe;
	font-weight: bold;
}

.new-board{
	background: #fafdcb;
	color: #248ea9;
}

.board input{
	width: 100%;
}

p {
    cursor: auto;
}
</style>