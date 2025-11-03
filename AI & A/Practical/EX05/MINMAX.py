import pygame,sys
pygame.init()
s=pygame.display.set_mode((600,650))
pygame.display.set_caption("Tic Tac Toe")
f=pygame.font.Font(None,60)
sf=pygame.font.Font(None,36)
b=[[""]*3for _ in range(3)]
h,c,w,t="X","O",None,h
W,B,R,U=(255,255,255),(0,0,0),(255,0,0),(0,0,255)
def d():
 s.fill(W)
 for i in range(1,3):pygame.draw.line(s,B,(150,i*100+100),(450,i*100+100),3);pygame.draw.line(s,B,(i*100+150,100),(i*100+150,400),3)
 for r in range(3):
  for c in range(3):
   if b[r][c]:txt=f.render(b[r][c],1,B);s.blit(txt,(c*100+185,r*100+125))
 m=sf.render(f"{w} wins! R-restart"if w else"It's a Draw! R-restart"if all(b[r][c]for r in range(3)for c in range(3))else"Your turn (X)",1,R if w else U)
 s.blit(m,(150,550))
def cw(bd):
 for r in range(3):
  if bd[r][0]==bd[r][1]==bd[r][2]!="":return bd[r][0]
 for c in range(3):
  if bd[0][c]==bd[1][c]==bd[2][c]!="":return bd[0][c]
 if bd[0][0]==bd[1][1]==bd[2][2]!="":return bd[0][0]
 if bd[0][2]==bd[1][1]==bd[2][0]!="":return bd[0][2]
def mm(bd,d,mx):
 w=cw(bd)
 if w==c:return 1
 if w==h:return-1
 if all(bd[r][c]for r in range(3)for c in range(3)):return 0
 if mx:bv=-999
 else:bv=999
 for r in range(3):
  for c in range(3):
   if bd[r][c]=="":
    bd[r][c]=c if mx else h
    v=mm(bd,d+1,not mx)
    bd[r][c]=""
    bv=max(bv,v)if mx else min(bv,v)
 return bv
def bm():
 global w,t
 bv,mv=-999,None
 for r in range(3):
  for c in range(3):
   if b[r][c]=="":
    b[r][c]=c
    v=mm(b,0,0)
    b[r][c]=""
    if v>bv:bv,mv=v,(r,c)
 if mv:b[mv[0]][mv[1]]=c;w=cw(b);t=h
def rs():global b,w,t;b=[[""]*3for _ in range(3)];w=None;t=h
rs()
r=1
while r:
 for e in pygame.event.get():
  if e.type==pygame.QUIT:r=0
  if e.type==pygame.KEYDOWN:
   if e.key==pygame.K_r:rs()
   if e.key==pygame.K_q:r=0
  if e.type==pygame.MOUSEBUTTONDOWN and t==h and not w:
   x,y=pygame.mouse.get_pos()
   if 150<x<450 and 100<y<400:
    rw,cl=(y-100)//100,(x-150)//100
    if b[rw][cl]=="":b[rw][cl]=h;w=cw(b)
    if not w and any(b[r][c]==""for r in range(3)for c in range(3)):t=c;bm()
 d();pygame.display.flip()
pygame.quit();sys.exit()