
import axios from '/src/axios.js';

export default class auth {

    login(data) {
        return axios({
            url: '/login',
            method: 'post',
            data: data,
        });
    }

    logout() {
        return axios({
            url: '/logout',
            method: 'post',
        });
    }


    changePassword(data) {
        return axios({
            url: '/change-password',
            method: 'post',
            data: data
        });
    }


}
