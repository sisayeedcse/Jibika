"use client";

import { useState } from 'react';
import { useJibikaStore } from '@/store/jibikaStore';
import { useRouter } from 'next/navigation';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

export default function RegisterPage() {
  const { registerWorker } = useJibikaStore();
  const router = useRouter();
  
  const [formData, setFormData] = useState({
    name: '',
    occupation: '',
    location: '',
    phone: '',
    experience: '',
    story: ''
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    registerWorker(formData);
    router.push('/worker');
  };

  return (
    <div className="container mx-auto px-4 py-12 max-w-xl">
      <Card>
        <CardHeader>
          <CardTitle className="text-2xl font-bold text-center">Register as a Worker</CardTitle>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium mb-1">Full Name</label>
              <input 
                type="text" 
                required 
                className="w-full p-2 border rounded-md"
                value={formData.name}
                onChange={e => setFormData({...formData, name: e.target.value})}
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Occupation</label>
              <input 
                type="text" 
                required 
                placeholder="e.g. Tailor, Farmer"
                className="w-full p-2 border rounded-md"
                value={formData.occupation}
                onChange={e => setFormData({...formData, occupation: e.target.value})}
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Location / District</label>
              <input 
                type="text" 
                required 
                className="w-full p-2 border rounded-md"
                value={formData.location}
                onChange={e => setFormData({...formData, location: e.target.value})}
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Phone Number</label>
              <input 
                type="tel" 
                required 
                className="w-full p-2 border rounded-md"
                value={formData.phone}
                onChange={e => setFormData({...formData, phone: e.target.value})}
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Experience (Years)</label>
              <input 
                type="text" 
                required 
                className="w-full p-2 border rounded-md"
                value={formData.experience}
                onChange={e => setFormData({...formData, experience: e.target.value})}
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1">Your Story & Needs</label>
              <textarea 
                required 
                rows={3}
                className="w-full p-2 border rounded-md"
                value={formData.story}
                onChange={e => setFormData({...formData, story: e.target.value})}
              ></textarea>
            </div>
            <Button type="submit" className="w-full">Create Account</Button>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}
