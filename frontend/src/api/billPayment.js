
import axios from '/src/axios.js';  

export default class billPayment {

  getBillsUnpaid() {
    return axios({
      url: `/bill/unpaid/`,
      method: 'get',
    });
  }

  payBills(data){
    return axios({
      url: '/bill/pay/',
      method: 'post',
      data: data,
      headers: { 'Content-Type': 'multipart/form-data' }
    });
  }

  getPayments(){
    return axios({
      url: `/payment/all/`,
      method: 'get',
    });
  }

  verifyPayment(data){
    return axios({
      url: '/payment/verify/',
      method: 'put',
      data: data,
    });
  }

  createGasBill(data){
    return axios({
      url: '/bill/create/gas',
      method: 'post',
      data: data,
      headers: { 'Content-Type': 'multipart/form-data' }
    });
  }

  createWaterBill(data){
    return axios({
      url: '/bill/create/agua',
      method: 'post',
      data: data,
      headers: { 'Content-Type': 'multipart/form-data' }
    });
  }

  createElectricityBill(data){
    return axios({
      url: '/bill/create/luz',
      method: 'post',
      data: data,
      headers: { 'Content-Type': 'multipart/form-data' }
    });
  }

}

