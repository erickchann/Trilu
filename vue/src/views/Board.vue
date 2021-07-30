<template>
    <div>
        <Nav></Nav>

        <div class="container">
            <div class="team-container">
                <div class="board-name">{{ board.name }}</div>
                <div class="member" v-for="member in board.members" :key="member.id" :title="member.first_name + ' ' + member.last_name" @click="state.deleteMember = true, data.memberId = member.id, data.memberName = `${member.first_name} ${member.last_name}`">{{ member.initial }}</div>

                <!-- show this buttons when a member clicked -->
                <a class="button" v-if="state.deleteMember" @click="deleteMember">Delete {{ data.memberName }}</a>
                <!-- back to normal state when cancel pressed -->
                <a class="button" v-if="state.deleteMember" @click="state.deleteMember = false">Cancel</a>

                <a class="button" v-if="!state.deleteMember" @click.prevent="state.addMember = true">
                    <span v-if="!state.addMember">+ Add member</span>
                    <input type="text" placeholder="Username" autofocus v-if="state.addMember" @blur="state.addMember = false" v-model="data.username" @keyup.enter="addMember">
                </a>
            </div>

            <div class="card-container">
                <div class="content">
                    <div class="list" v-for="list in board.lists" :key="list.id">
                        <header @click="data.editListId = list.id, data.newListName = list.name">
                            <span v-if="data.editListId != list.id">{{ list.name }}</span>
                            <input type="text" placeholder="Are you sure want to delete?" autofocus v-if="data.editListId == list.id" @keyup.enter="editList" @blur="data.editListId = ''" v-model="data.newListName">
                        </header>

                        <div class="cards">
                            <div class="card" v-for="card in list.cards" :key="card.id">
                                <div class="card-content" @click="data.cardId = card.card_id, data.cardName = card.task, data.cardListId = list.id">
                                    <span v-if="data.cardId != card.card_id">{{ card.task }}</span>
                                    <input type="text" placeholder="Are you sure want to delete?" autofocus v-if="data.cardId == card.card_id" v-model="data.cardName" @blur="data.cardId = ''" @keyup.enter="editCard">
                                </div>
                                <div class="control">
                                    <span @click="moveUp(card.card_id)">&uarr;</span>
                                    <span @click="moveDown(card.card_id)">&darr;</span>
                                </div>
                            </div>
                        </div>

                        <div class="button" @click="data.cardList = list.id" v-if="data.cardList != list.id">+ Add new card</div>
                        <input type="text" placeholder="New card" autofocus v-if="data.cardList == list.id" @blur="data.cardList = ''" v-model="data.cardName" @keyup.enter="addCard">

                        <div class="control">
                            <span @click="moveLeft(list.id)">&larr;</span>
                            <span @click="moveRight(list.id)">&rarr;</span>
                        </div>
                    </div>

                    <div class="list button" @click="state.addList = true">
                        <span v-if="!state.addList">+ Add another list</span>
                        <span v-if="state.addList"><input type="text" placeholder="New List Name" autofocus @blur="state.addList = false" @keyup.enter="addList" v-model="data.listName"></span>
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
            id: this.$route.params.id,
            state: {
                addMember: false,
                deleteMember: false,
                addList: false,
            },
            data: {
                username: '',
                memberName: '',
                memberId: '',

                listName: '',
                newListName: '',
                editListId: '',

                cardName: '',
                cardList: '',
                cardId: '',
                cardListId: ''
            },
            board: ''
        }
    },
    methods: {
        getBoard() {
            axios.get(`board/${this.id}?token=${this.token}`)
                .then(res => {
                    this.board = res.data;
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
        },
        addMember() {
            axios.post(`board/${this.id}/member?token=${this.token}`, {username: this.data.username})
                .then(res => {
                    this.getBoard();
                    
                    this.data.username = '';
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
        },
        deleteMember() {
            axios.delete(`board/${this.id}/member/${this.data.memberId}?token=${this.token}`)
                .then(res => {
                    this.getBoard();

                    this.data.memberId = '';
                    this.data.memberName = '';
                    this.state.deleteMember = false;
                })
                .catch(err => {
                    console.log(err.response.data.message);
                })
        },
        addList() {
            axios.post(`board/${this.id}/list?token=${this.token}`, {name: this.data.listName})
                .then(res => {
                    this.getBoard();
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
        },
        editList() {
            if (this.data.newListName) {
                axios.put(`board/${this.id}/list/${this.data.editListId}?token=${this.token}`, {name: this.data.newListName})
                    .then(res => {
                        this.getBoard();
                    })
                    .catch(err => {
                        console.log(err.response.data.message);
                    });
            } else {
                axios.delete(`board/${this.id}/list/${this.data.editListId}?token=${this.token}`)
                    .then(res => {
                        this.getBoard();
                    })
                    .catch(err => {
                        console.log(err.response.data.message);
                    });
            }
        },
        addCard() {
            axios.post(`board/${this.id}/list/${this.data.cardList}/card?token=${this.token}`, {task: this.data.cardName})
                .then(res => {
                    this.getBoard();
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
        },
        editCard() {
            if (this.data.cardName) {
                axios.put(`board/${this.id}/list/${this.data.cardListId}/card/${this.data.cardId}?token=${this.token}`, {task: this.data.cardName})
                .then(res => {
                    this.getBoard();
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
            } else {
                axios.delete(`board/${this.id}/list/${this.data.cardListId}/card/${this.data.cardId}?token=${this.token}`)
                .then(res => {
                    this.getBoard();
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
            }
        },
        moveRight(id) {
            axios.post(`board/${this.id}/list/${id}/right?token=${this.token}`)
                .then(res => {
                    this.getBoard();
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
        },
        moveLeft(id) {
            axios.post(`board/${this.id}/list/${id}/left?token=${this.token}`)
                .then(res => {
                    this.getBoard();
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
        },
        moveUp(id) {
            axios.post(`card/${id}/up?token=${this.token}`)
                .then(res => {
                    this.getBoard();
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
        },
        moveDown(id) {
            axios.post(`card/${id}/down?token=${this.token}`)
                .then(res => {
                    this.getBoard();
                })
                .catch(err => {
                    console.log(err.response.data.message);
                });
        }
    },
    created() {
        this.getBoard();
    }
}
</script>

<style scoped>
.container {
  position: absolute;
  height: 90vh;
  width: 100vw;
}

.team-container {
  margin-left: 1em;
  margin-right: 1em;
  margin-top: 1em;
}

.team-container div {
  display: inline-block;
}

.team-container .board-name {
  margin-right: 1em;
  font-weight: bold;
  font-size: 1.2em;
}

.team-container .member {
  background: #aee7e8;
  font-weight: bold;
  color: #111;
  margin-left: -0.7em;
  border: 1px solid #ffe;
  border-radius: 50%;
  padding: 0.3em;
  cursor: pointer;
}

.team-container .member:hover {
  box-shadow: inset 0 0 0 20px rgba(0, 0, 0, 0.3);
}

.team-container .button {
  margin: 0 5px;
  padding: 0.3em 0.5em;
  border-radius: 5px;
  cursor: pointer;
}

.card-container {
  margin-left: 1em;
  margin-right: 1em;
  margin-top: 1em;
}

.content {
  overflow-x: scroll;
  overflow-y: hidden;
  white-space: nowrap;
  height: 80vh;
  display: flex;
  align-items: flex-start;
}

.list {
  word-wrap: break-word;
  white-space: normal;
  display: inline-block;
  min-width: 272px;
  padding: 0.5em;
  margin-right: 0.7em;
  background: #28c3d4;
  border-radius: 3px;
  color: #ffe;
  box-shadow: inset 0 0 0 100vh rgba(0, 0, 0, 0.2);
}

.list > :last-child {
  margin-right: 0 !important;
}

.list header {
  font-size: 1.2em;
  font-weight: bold;
  padding-left: 0.5em;
}

.list header:hover {
  color: #248ea9;
  cursor: pointer;
}

.list .cards {
  margin-top: 0.5em;
  max-height: 55vh;
  overflow-y: auto;
  padding: 0.2em;
}

.list .card {
  display: flex;
  margin: 0.5em 0;
  background: #ffe;
  border-radius: 3px;
  color: #111;
}

.list .card .card-content {
  padding: 0.5em;
  flex: 6;
}

.list .card-content:hover {
  box-shadow: inset 0 0 0 1000px rgba(0, 0, 0, 0.2);
  cursor: pointer;
}

.list .card .control {
  display: flex;
  justify-content: flex-end;
  flex-grow: 1;
  flex: 1;
  margin-right: 5px;
}

.list .card .control span {
  font-size: 1.5em;
  padding: 2px;
  margin: 0px;
  white-space: nowrap;
}

.list .card .control span:hover,
.list > .control span:hover {
  color: #248ea9;
  cursor: pointer;
}

.list > .control {
  margin-top: -5px;
  text-align: center;
}

.list > .control span {
  font-size: 2em;
  font-weight: bold;
}

.list .button {
  margin-top: 0.2em;
  padding: 5px;
  border-radius: 3px;
}

.button {
  background: #28c3d4;
  color: #fafdcb;
  box-shadow: inset 0 0 0 272px rgba(0, 0, 0, 0.4);
  color: #ffe;
  cursor: pointer;
}

.button:hover {
  box-shadow: inset 0 0 0 1000px rgba(0, 0, 0, 0.6);
}

.list input,
.card input {
  width: 80%;
}

.card-on-edit .list header:after {
  content: "(click here to move card)";
  font-size: 0.8em;
}
</style>