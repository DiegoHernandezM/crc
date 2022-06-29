import React from 'react';
import Layout from '@/Shared/Layout';
import MaterialTable from '@material-table/core';
import { Inertia } from '@inertiajs/inertia';
import {Grid} from "@material-ui/core";
import {InertiaLink, usePage} from "@inertiajs/inertia-react";
import {Delete, Visibility as ShowIcon, Visibility as ViewIcon} from '@material-ui/icons';
import materialTableLocaleES from "../../Shared/MaterialTableLocateES";

const Index = () => {
    const { typeAssociate } = usePage().props;
    const {
        data,
        meta: { links }
    } = typeAssociate;


    function destroy(id) {
        if (confirm('¿Esta de acuerdo en eliminar el tipo de asociado?')) {
            Inertia.delete(route('typeassociate.destroy', id));
        }
    }

    return (
        <div>
            <h1 className="mb-8 text-3xl font-bold">Tipo de Asociado</h1>
            <div className="flex items-center justify-between mb-6">
                <InertiaLink
                    className="btn-indigo focus:outline-none"
                    href={route('typeassociate.create')}
                >
                    <span>Crear</span>
                    <span className="hidden md:inline"> Tipo de Asociado</span>
                </InertiaLink>
            </div>
            <MaterialTable
                columns={[
                    { title: 'Nombre', field: 'name' },
                ]}
                data={data}
                title="Tipo de asociado"
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
                            Inertia.get(route('typeassociate.edit', rowData.id));
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

Index.layout = page => <Layout title="Tipo de Asociado" children={page} />;

export default Index;
