import axios from "axios";

const CHECKIN_PATH = '/checkin';
const USER_PATH = '/users';
const REPORT_PATH = '/reports';
const DASHBOARD_PATH = '/dashboard';
const ASSOCIATES_PATH = '/associates';
const SUBAREAS_PATH = '/subarea';
const TEAMS_PATH = '/teams';

export const getAssistences = (
  associate, page, pageSize, dateInit, dateEnd ) => {
  return axios.get(`${CHECKIN_PATH}/associate/${associate}`, {
    params : {
      page: page,
      per_page: pageSize,
      dateInit,
      dateEnd,
    }
    })
    .then(response => {
      return response.data;
    })
    .catch(error => {
      console.log(error)

    });
};

export const validateUserPassword = (password) => {
  return axios.get(`${USER_PATH}/validate/${password}`)
    .then(response => {
      return response.data;
    })
    .catch(error => {
      console.log(error);
    });
};

export const updateCheckin = (data, comments) => {
  return axios.post(`${CHECKIN_PATH}/update/`, {
    data,
    comments
  })
    .then(response => {
      return response.data;
    })
    .catch(error => {
      console.log(error);
    });
};

export const newCheckin = (data) => {
  return axios.post(`${CHECKIN_PATH}/store/`, {
    data
  })
    .then(response => {
      return response.data;
    })
    .catch(error => {
      console.log(error);
    });
};

export const getReportExcel = (filters) => {
  return axios.get(`${REPORT_PATH}/associate/historic/`, {
    params: {
      id: filters.id,
      dateInit: filters.dateInit,
      dateEnd: filters.dateEnd
    },
    responseType: 'blob',
  })
    .then(response => {
      return Promise.resolve(response);
    })
    .catch(error => {
      console.log(error);
    });
};

export const getReportPicking = (filters) => {
  return axios.get(`${REPORT_PATH}/exportpickingbonus`, {
    params: {
      dateInit: filters.dateInit,
      dateEnd: filters.dateEnd
    },
    responseType: 'blob',
  })
    .then(response => {
      return Promise.resolve(response);
    })
    .catch(error => {
      console.log(error);
    });
};

export const getReportGeneralExcel = (filters) => {
    return axios.get(`${REPORT_PATH}/historic/`, {
        params: {
            dateInit: filters.dateInit,
            dateEnd: filters.dateEnd
        },
        responseType: 'blob',
    })
        .then(response => {
            return Promise.resolve(response);
        })
        .catch(error => {
            console.log(error);
        });
};

export const getDataExtraHours = (filters) => {
  return axios.get(`${REPORT_PATH}/dataextrahours/`, {
    params: {
        dateInit: filters.dateInit,
        dateEnd: filters.dateEnd,
        view: false
    }
  })
    .then(response => {
      return Promise.resolve(response);
    })
    .catch(error => {
      console.log(error);
    });
};

export const getExcelExtraHours = (filters) => {
    return axios.get(`${REPORT_PATH}/exportextrahours/`, {
        params: {
            dateInit: filters.dateInit,
            dateEnd: filters.dateEnd,
        },
        responseType: 'blob',
    })
        .then(response => {
            return Promise.resolve(response);
        })
        .catch(error => {
            console.log(error);
        });
};

export const getBestAssociatesWeek = () => {
    return axios.get(`${DASHBOARD_PATH}/besthours/`)
        .then(response => {
            return Promise.resolve(response);
        })
        .catch(error => {
            console.log(error);
        });
};

export const getBestAbsencesWeek = () => {
    return axios.get(`${DASHBOARD_PATH}/absences/`)
        .then(response => {
            return Promise.resolve(response);
        })
        .catch(error => {
            console.log(error);
        });
};

export const getBestAbsencesByDay = () => {
    return axios.get(`${DASHBOARD_PATH}/absencesday/`)
        .then(response => {
            return Promise.resolve(response);
        })
        .catch(error => {
            console.log(error);
        });
};

export const getSorterProdWeek = () => {
    return axios.get(`${DASHBOARD_PATH}/prodsorter/`)
        .then(response => {
            return Promise.resolve(response);
        })
        .catch(error => {
            console.log(error);
        });
};

export const getPickingProdWeek = () => {
    return axios.get(`${DASHBOARD_PATH}/prodpicking/`)
        .then(response => {
            return Promise.resolve(response);
        })
        .catch(error => {
            console.log(error);
        });
};

export const loadPickingProductivity = (data) => {
    return axios.post(`${REPORT_PATH}/loadpickingproductivity`,{
            data,
        })
        .then(response => {
            return Promise.resolve(response.data);
        })
        .catch(error => {
            Log.error(
                loadPickingProductivity.name,
                error.message,
                error.response ? error.response : error,
                !(error.response && error.response.status === 404),
            );
            return Promise.reject();
        });
};

export const getPickingBonus = (filters) => {
  return axios.get(`${REPORT_PATH}/datapickingbonus/`, {
    params: {
        dateInit: filters.dateInit,
        dateEnd: filters.dateEnd,
    }
  })
    .then(response => {
      return Promise.resolve(response);
    })
    .catch(error => {
      console.log(error);
    });
};

export const updateSubareaAssociate = (id, oldArea, newArea) => {
  return axios.post(`${ASSOCIATES_PATH}/subarea/${id}`, {
      oldArea,
      newArea
  })
      .then(response => {
          return response.data;
      })
      .catch(error => {
          console.log(error);
      });
};

export const moveTeam = (associateIds, newArea, newShift) => {
  return axios.post(`associatelist/moveteam`, {
      associateIds,
      newArea,
      newShift
  })
      .then(response => {
          return response.data;
      })
      .catch(error => {
          console.log(error);
      });
};

export const getAssociates = (status, shift, search) => {
  return axios.get(`/associatelist`, {
    params: {
      subarea: status ? status : null,
      shift: shift ? shift : null,
      search: search ? search.trim(): null,
    }
  })
      .then(response => {
          return response.data;
      })
      .catch(error => {
          console.log(error);
      });
};

export const getSubareasData = () => {
  return axios.get(`${TEAMS_PATH}/data/`)
      .then(response => {
          return response.data;
      })
      .catch(error => {
          console.log(error);
      });
};

export const uploadExcelWamas = (fromData,config) => {
    return axios.post(`${REPORT_PATH}/sorter/datawamas`,fromData,config)
        .then(response => {
            return response;
        })
        .catch(error => {
            console.log(error);
        });
};

export const updateStops = (data, type) => {
    return axios.post(`${REPORT_PATH}/sorter/stops`, {
        waves: data,
        type: type,
    })
        .then(response => {
            return response.data;
        })
        .catch(error => {
            console.log(error);
        });
};

export const calculateTimesByWave = (data) => {
    return axios.post(`${REPORT_PATH}/sorter/calculatetimes`, {
        waves: data,
    })
        .then(response => {
            return response.data;
        })
        .catch(error => {
            console.log(error);
        });
};

export const caclulateBonus = (data) => {
    return axios.post(`${REPORT_PATH}/sorter/calculatebonus`, {
        waves: data,
    })
        .then(response => {
            return response.data;
        })
        .catch(error => {
            console.log(error);
        });
};


export const getSubareas = () => {
  return axios.get(`${SUBAREAS_PATH}/area/`)
      .then(response => {
          return response.data;
      })
      .catch(error => {
          console.log(error);
      });
};

export const getSorterBonus = (filters) => {
    return axios.get(`${REPORT_PATH}/getbonusdata/`, {
        params: {
            dateInit: filters.dateInit,
            dateEnd: filters.dateEnd,
        }
    })
        .then(response => {
            return Promise.resolve(response);
        })
        .catch(error => {
            console.log(error);
        });
};

export const getReportSorter = (filters) => {
    return axios.get(`${REPORT_PATH}/exportsorterbonus`, {
        params: {
            dateInit: filters.dateInit,
            dateEnd: filters.dateEnd
        },
        responseType: 'blob',
    })
        .then(response => {
            return Promise.resolve(response);
        })
        .catch(error => {
            console.log(error);
        });
};

export const getReportStaffSorter = (filters) => {
    return axios.get(`${REPORT_PATH}/exportsorterstaffbonus`, {
        params: {
            dateInit: filters.dateInit,
            dateEnd: filters.dateEnd
        },
        responseType: 'blob',
    })
        .then(response => {
            return Promise.resolve(response);
        })
        .catch(error => {
            console.log(error);
        });
};

export const getPendingProds = () => {
    return axios.get(`${REPORT_PATH}/getpendingprods`)
        .then(response => {
            return response.data;
        })
        .catch(error => {
            console.log(error);
        });
};

export const processProd = (day) => {
    return axios.post(`${REPORT_PATH}/calculatebonussorter`,  {
        init: day
    })
        .then(response => {
            return response.data;
        })
        .catch(error => {
            console.log(error);
        });
};

export const getSorterStaffBonus = (init) => {
    return axios.get(`${REPORT_PATH}/getbonusstaff/`, {
        params: {
            init,
        }
    })
        .then(response => {
            return Promise.resolve(response);
        })
        .catch(error => {
            console.log(error);
        });
};

export const getSubareaById = (area) => {
    return axios.get(`${SUBAREAS_PATH}/area/${area}`)
        .then(response => {
            return Promise.resolve(response.data);
        })
        .catch(error => {
            console.log(error);
        });
};

export const setRangeShift = (init, end) => {
  return axios.get(`/range`, {
    params : {
      init,
      end
    }
  })
    .then(response => {
      return Promise.resolve(response.data);
    })
    .catch(error => {
      console.log(error);
    });
};
