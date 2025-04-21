import axios from '/src/axios.js';  // Import the axios instance

export default class contract {

    getAllProperties(){
        return axios({
            url: `/property/all`,
            method: 'get',
        });
    }

    switchPropertyApplications(data){
        return axios({
            url: '/property/activate/applications',
            method: 'put',
            data: data,
        });
    }

    checkIfPropertyApplicationOpen(data){
        return axios({
            url: `/property/check/application/open`,
            method: 'get',
            params: data
        });
    }

}
