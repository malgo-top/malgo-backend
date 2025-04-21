// src/axios.js

import axios from 'axios'
import store from '/src/store'  // Make sure to import the Vuex store

const axiosInstance = axios.create({
  baseURL: 'http://localhost:8000/api/',  // Example base URL
  // headers: {
  //   'Content-Type': 'application/json'
  // }
})

// Add a request interceptor to add the token (if any) to the headers
axiosInstance.interceptors.request.use((config) => {
    // Access the token from the Vuex store state
    //   const token = store.state.token || null  // or use store.getters.token if using getters
    const token = store.getters.getToken || null  // or use store.getters.token if using getter
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
    }, (error) => {
    return Promise.reject(error)
    })

// Add a response interceptor to return only the data part of the response
axiosInstance.interceptors.response.use((response) => {
    return response.data;  // Here we return only the `data` part of the response
  }, (error) => {
    return Promise.reject(error);
  });

export default axiosInstance;
