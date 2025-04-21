// src/api/swapResource.js

import axios from '/src/axios.js';  // Import the axios instance

export default class contract {

    getContracts(){
        return axios({
            url: `/contract/all`,
            method: 'get',
        });
    }

    createContract(data){
        return axios({
            url: '/contract/create',
            method: 'post',
            data: data,
            headers: { 'Content-Type': 'multipart/form-data' }
        });
    }

    terminarContrato(data){
        return axios({
            url: '/contract/terminate',
            method: 'put',
            data: data,
        });
    }

}
