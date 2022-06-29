import React, {useEffect} from 'react';
import {InertiaLink, usePage} from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import SingleSelect from 'react-select';

import Card from "../../../Shared/DashboardComponents/Card/Card.js";
import CardBody from "../../../Shared/DashboardComponents/Card/CardBody.js";
import CardHeader from "../../../Shared/DashboardComponents/Card/CardHeader.js";
import CardIcon from "../../../Shared/DashboardComponents/Card/CardIcon.js";
import CardFooter from "../../../Shared/DashboardComponents/Card/CardFooter.js";
import {Typography, Grid, TextField, FormControl, Box} from '@material-ui/core';
import moment from "moment";
import materialTableLocaleES from "../../../Shared/MaterialTableLocateES";
import {MTableToolbar} from "@material-table/core";
import MaterialTable from "@material-table/core";
import {getSubareasData, updateSubareaAssociate} from "../../../Api/CheckinService/CheckinApi";
import Autocomplete from "@material-ui/lab/Autocomplete/Autocomplete";

const Dashboard = () => {
    const { members } = usePage().props;
    const { subareas } = usePage().props;
    const { membersSubarea } = usePage().props;
    const [ allSubareas , setAllSubareas ] = React.useState(subareas);
    const [ membersSubareas , setMemberSubareas ] = React.useState(membersSubarea);
    const [ options, setOptions ] = React.useState([]);

    useEffect(() => {
        let subs = [];
        allSubareas.map(function (subarea, index, array) {
            subs.push({ value: subarea.id, label : subarea.name })
        });
        setOptions(subs);
    }, []);

    function init() {
      getSubareasData()
          .then(response => {
              setAllSubareas(response['subareas']);
              setMemberSubareas(response['members']);
          })
          .catch(error => {
              console.log(error);
          });
    }

    function renderSubarea(){
      return allSubareas.map(function (subarea, index, array) {
        return (
          <Grid item xs={6} key={subarea.id}>
            <Card style={{height:'430px'}}>
              <CardHeader color="success" stats icon>
                <CardIcon color="success">
                    <h1>{subarea.name}</h1>
                </CardIcon>
                <Typography variant="h6" color="primary">
                </Typography>
              </CardHeader>
              <CardBody>
                <MaterialTable
                  columns={[
                    { title: 'No. Empleado', field: 'employee_number', editable: 'never'},
                    { title: 'Nombre', field: 'name', editable: 'never'},
                    {
                      title: 'Subarea',
                      field: 'subarea_id',
                      render: rowData => {
                        return rowData.subarea;
                      },
                      editComponent: (props) => (
                        <SingleSelect
                            options={options}
                            value={options.find( id => id.value === props.value )}
                            onChange={value => props.onChange(value)}
                        />
                      ),
                    },
                  ]}
                  options={{
                    search: true,
                    actionsColumnIndex: -1,
                    showTitle: false,
                    toolbar: true,
                    padding: 'dense',
                    pageSize: 5,
                  }}
                  editable={{
                    onRowUpdate: (newData, oldData) =>
                      new Promise((resolve, reject) => {
                        setTimeout(() => {
                          updateSubareaAssociate(newData.id, oldData.subarea_id, newData.subarea_id.value)
                            .then(response => {
                                init();
                                resolve();
                            })
                            .catch(error => {
                                console.log(error);
                                reject();
                          });
                        }, 1000)
                      }),
                  }}
                  localization={materialTableLocaleES}
                  data={membersSubareas[subarea.id]}
                  title="Horas extra"
                />
              </CardBody>
            </Card>
          </Grid>
        )
      });
    }

    useEffect(() => {

    }, []);

    return (
        <div>
            <h1 className="mb-8 text-3xl font-bold">
                <InertiaLink
                    href={route('teams')}
                    className="text-indigo-600 hover:text-indigo-700"
                >
                    Equipos
                </InertiaLink>
                <span className="mx-2 font-medium text-indigo-600">/</span>
                Picking
            </h1>
            <Grid container spacing={1} direction="row" justifyContent="center" alignItems="baseline">
                {renderSubarea()}
            </Grid>
        </div>
    );
};
Dashboard.layout = page => <Layout title="Equipos" children={page} />;

export default Dashboard;
