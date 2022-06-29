import React from 'react';
import 'date-fns';
import Helmet from 'react-helmet';
import { InertiaLink, usePage, useForm } from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';

const Edit = () => {
    const { typeAssociate } = usePage().props;
    const { data, setData, errors, put, processing } = useForm({
        name: typeAssociate.name || '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        put(route('typeassociate.update', typeAssociate.id));
    }

    return (
        <div>
            <Helmet title={data.name} />
            <h1 className="mb-8 text-3xl font-bold">
                <InertiaLink
                    href={route('typeassociate')}
                    className="text-indigo-600 hover:text-indigo-700"
                >
                    Tipo de Asociado
                </InertiaLink>
                <span className="mx-2 font-medium text-indigo-600">/</span>
                {data.name}
            </h1>
            <div className="max-w-3xl overflow-hidden bg-white rounded shadow">
                <form onSubmit={handleSubmit}>
                    <div className="flex flex-wrap p-8 -mb-8 -mr-6">
                        <TextInput
                            className="w-full pb-8 pr-6 lg:w-1/2"
                            label="Nombre"
                            name="name"
                            errors={errors.name}
                            value={data.name}
                            onChange={e => setData('name', e.target.value)}
                        />
                    </div>
                    <div className="flex items-center px-8 py-4 bg-gray-100 border-t border-gray-200">
                        <LoadingButton
                            loading={processing}
                            type="submit"
                            className="ml-auto btn-indigo"
                        >
                            Actualizar
                        </LoadingButton>
                    </div>
                </form>
            </div>
        </div>
    );
};

Edit.layout = page => <Layout children={page} />;

export default Edit;
