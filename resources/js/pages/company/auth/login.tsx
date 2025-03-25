import { useForm } from '@inertiajs/react';
import { FormEvent, useEffect } from 'react';
import { Head, Link } from '@inertiajs/react';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';

export default function CompanyLogin() {
  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  useEffect(() => {
    return () => {
      reset('password');
    };
  }, []);

  function submit(e: FormEvent) {
    e.preventDefault();
    post(route('company.login'));
  }

  return (
    <>
      <Head title="Company Login" />
      
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="w-full max-w-md">
          <Card>
            <CardHeader>
              <CardTitle className="text-2xl font-bold text-center">Company Login</CardTitle>
              <CardDescription className="text-center">Sign in to your company account</CardDescription>
            </CardHeader>
            <CardContent>
              <form onSubmit={submit} className="space-y-6">
                <div className="space-y-2">
                  <Label htmlFor="email">Email</Label>
                  <Input 
                    id="email"
                    type="email" 
                    name="email"
                    value={data.email}
                    className="mt-1 block w-full"
                    autoComplete="username"
                    onChange={(e) => setData('email', e.target.value)}
                  />
                  {errors.email && <p className="text-sm text-red-600">{errors.email}</p>}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="password">Password</Label>
                  <Input 
                    id="password"
                    type="password" 
                    name="password"
                    value={data.password}
                    className="mt-1 block w-full"
                    autoComplete="current-password"
                    onChange={(e) => setData('password', e.target.value)}
                  />
                  {errors.password && <p className="text-sm text-red-600">{errors.password}</p>}
                </div>

                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <Checkbox 
                      id="remember"
                      name="remember" 
                      checked={data.remember}
                      onCheckedChange={(checked) => setData('remember', !!checked)}
                    />
                    <Label htmlFor="remember" className="text-sm">Remember me</Label>
                  </div>
                  
                  {route().has('company.password.request') && (
                    <Link href={route('company.password.request')} className="text-sm text-blue-600 hover:underline">
                      Forgot your password?
                    </Link>
                  )}
                </div>

                <Button type="submit" className="w-full" disabled={processing}>
                  Log in
                </Button>
              </form>
            </CardContent>
            <CardFooter className="justify-center">
              <p className="text-sm text-gray-600">
                Don't have an account?{' '}
                <Link href={route('company.register')} className="text-blue-600 hover:underline">
                  Register as a company
                </Link>
              </p>
            </CardFooter>
          </Card>
        </div>
      </div>
    </>
  );
}
