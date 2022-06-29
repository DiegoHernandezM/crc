import React, {useEffect} from 'react';
import { InertiaLink, usePage } from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import materialTableLocaleES from "../../Shared/MaterialTableLocateES";
import {Delete, Visibility as ShowIcon} from "@material-ui/icons";
import {Inertia} from "@inertiajs/inertia";
import MaterialTable from "@material-table/core";
import moment from "moment";
import {getAssociates} from "../../Api/CheckinService/CheckinApi";

const Index = () => {
  const { associates, auth } = usePage().props;
  const [dataAssociates, setDataAssociates] = React.useState([]);
  const {
    data
  } = associates;

  useEffect(() => {
    getAssociates()
      .then(response => {
          setDataAssociates(response);
      })
      .catch(error => {
            console.log(error)
      });

  }, []);

  function destroy(id) {
    if (confirm('¿Esta de acuerdo en eliminar el asociado?')) {
        Inertia.get(route('associates.destroy', id));
    }
  }
  return (
    <div>
      <h1 className="mb-8 text-3xl font-bold">Asociados</h1>
      { auth.user.can['Asociados.Crear'] &&
        <div className="flex items-center justify-between mb-6">
          <InertiaLink
            className="btn-indigo focus:outline-none"
            href={route('associates.create')}
          >
            <span>Agregar</span>
            <span className="hidden md:inline"> Asociado</span>
          </InertiaLink>
        </div>  
      }      
      <MaterialTable
        columns={[
          { title: 'No. Empleado', field: 'employee_number' },
          { title: 'Nombre', field: 'name' },
          { title: 'Horario', field: 'shift' },
          { title: 'Subarea', field: 'subarea' },
        ]}
        data={dataAssociates}
        title="Horarios"
        localization={materialTableLocaleES}
        options={{
          search: true,
          showTitle: false,
          padding: "dense",
          pageSize: 20,
          actionsColumnIndex: -1,
        }}
        actions={[ auth.user.can['Asociados.Actualizar'] &&
          {
            icon: (rowData) => (
                <ShowIcon color='primary' className="icon-small" />
            ),
            tooltip: 'Detalle',
            onClick: (event, rowData) => {
                Inertia.get(route('associates.edit', rowData.id));
            }
          },
          auth.user.can['Asociados.Borrar'] && {
            icon: (rowData) => (
                <Delete color='secondary' className="icon-small" />
            ),
            tooltip: 'Eliminar',
            onClick: (event, rowData) => {
                destroy(rowData.id);
            }
          }
        ]}
      />
    </div>
  );
};

Index.layout = page => <Layout title="Asociados" children={page} />;

export default Index;
