import React from 'react';
import Layout from '@/Shared/Layout';
import MaterialTable from '@material-table/core';
import { Inertia } from '@inertiajs/inertia';
import {Grid} from "@material-ui/core";
import {InertiaLink, usePage} from "@inertiajs/inertia-react";
import {Delete, Visibility as ShowIcon, Visibility as ViewIcon} from '@material-ui/icons';
import materialTableLocaleES from '../../Shared/MaterialTableLocateES';

const Index = () => {
  const { bonos } = usePage().props;
  const {
    data
  } = bonos;

  function destroy(id) {
    if (confirm('¿Esta de acuerdo en eliminar?')) {
      Inertia.delete(route('board.destroy', id));
    }
  }

  return (
    <div>
      <h1 className="mb-8 text-3xl font-bold">Tablero de bonos</h1>
      <div className="flex items-center justify-between mb-6">
        <InertiaLink
          className="btn-indigo focus:outline-none"
          href={route('board.create')}
        >
          <span>Crear</span>
          <span className="hidden md:inline"> Bono</span>
        </InertiaLink>
      </div>
      <MaterialTable
        columns={[
          { title: 'Area', field: 'area.name',
              render: rowData => {
                  let area = (rowData.area === null) ? 'Administrador' : rowData.area.name;
                  return area;
              }
          },
          { title: 'Subarea', field: 'subarea.name',
            render: rowData => {
                let subarea = (rowData.area === null) ? 'Administrador' : rowData.subarea.name;
                return subarea;
            }
          },
          { title: 'Cantidad', field: 'quantity' },
          { title: 'bono', field: 'bono' },
        ]}
        data={data}
        title="Tablero"
        localization={materialTableLocaleES}
        options={{
          search: true,
          showTitle: false,
          actionsColumnIndex: -1,
          pageSize: 10,
          padding: 'dense',
        }}
        actions={[
          {
            icon: (rowData) => (
              <ShowIcon color='primary' className="icon-small" />
            ),
            tooltip: 'Detalle',
            onClick: (event, rowData) => {
              Inertia.get(route('board.edit', rowData.id));
            }
          },
          {
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

Index.layout = page => <Layout title="Tablero" children={page} />;

export default Index;
