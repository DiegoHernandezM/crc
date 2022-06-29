import React from 'react';
import Layout from '@/Shared/Layout';
import MaterialTable from '@material-table/core';
import { Inertia } from '@inertiajs/inertia';
import {Grid} from "@material-ui/core";
import {InertiaLink, usePage} from "@inertiajs/inertia-react";
import {Delete, Visibility as ShowIcon, Visibility as ViewIcon} from '@material-ui/icons';
import materialTableLocaleES from '../../Shared/MaterialTableLocateES';

const Index = () => {
    const { areas } = usePage().props;
    const {
        data,
        meta: { links }
    } = areas;

    function destroy(id) {
        if (confirm('¿Esta de acuerdo en eliminar?')) {
            Inertia.delete(route('area.destroy', id));
        }
    }

    return (
        <div>
            <h1 className="mb-8 text-3xl font-bold">Areas</h1>
            <div className="flex items-center justify-between mb-6">
                <InertiaLink
                    className="btn-indigo focus:outline-none"
                    href={route('area.create')}
                >
                    <span>Crear</span>
                    <span className="hidden md:inline"> Area</span>
                </InertiaLink>
            </div>
            <MaterialTable
                columns={[
                    { title: 'Nombre', field: 'name' },
                ]}
                data={data}
                title="Areas"
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
                            Inertia.get(route('area.edit', rowData.id));
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

Index.layout = page => <Layout title="Areas" children={page} />;

export default Index;
