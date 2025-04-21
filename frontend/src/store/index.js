import { createStore } from 'vuex';

const store = createStore({
  state: {
    billsUnpaid: [],
    payments: [],
    tenantApplications: [],
    properties: [

    ],
    contracts: [],
    token: null,
    // user: {
    //   role: 'admin'
    // },
    // user: {
    //   role: 'tenant'
    // },
    user:  {},
  },
  getters: {
    getBillsUnpaid: (state) => state.billsUnpaid,
    getPayments: (state) => state.payments,
    getTenantApplications: (state) => state.tenantApplications,
    getProperties: (state) => state.properties,
    getContracts: (state) => state.contracts,
    getUser: (state) => state.user,
    getToken: (state) => state.token,
    userRole: state => state.user.role
  },
  mutations: {
    setBillsUnpaid: (state, billsUnpaid) => {
      state.billsUnpaid = billsUnpaid;
    },
    setPayments: (state, payments) => {
      state.payments = payments;
    },
    setToken: (state, token) => {
        state.token = token;
    },
    setTenantApplications: (state, applications) => {
      state.tenantApplications = applications;
    },
    setProperties: (state, properties) => {
      state.properties = properties;
    },
    setContracts: (state, contracts) => {
      state.contracts = contracts;
    },
    setUser: (state, user) => {
      state.user = user;
    },
  },
});

export default store;
